<?php
namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

// OpenAI Realtime API WebSocket Implementation
class RealtimeAIService
{
    private $websocket;
    private $apiKey;
    private $isConnected = false;
    private $eventQueue = [];
    
    // OpenAI Realtime API URL
    const REALTIME_URL = 'wss://api.openai.com/v1/realtime';
    const MODEL = 'gpt-4o-realtime-preview-2024-10-01';
    
    public function __construct()
    {
        $this->apiKey = env('AI_API_KEY'); // Your OpenAI API key
    }
    
    /**
     * Connect to OpenAI Realtime WebSocket
     */
    public function connect()
    {
        try {
            Log::info('Connecting to OpenAI Realtime API...');
            
            // WebSocket URL with model parameter
            $url = self::REALTIME_URL . '?model=' . self::MODEL;
            
            // Headers for authentication
            $headers = [
                'Authorization: Bearer ' . $this->apiKey,
                'OpenAI-Beta: realtime=v1'
            ];
            
            // Create WebSocket connection
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
                'http' => [
                    'header' => implode("\r\n", $headers)
                ]
            ]);
            
            // Connect to WebSocket
            $this->websocket = $this->createWebSocketConnection($url, $headers);
            
            if (!$this->websocket) {
                throw new Exception('Failed to establish WebSocket connection');
            }
            
            $this->isConnected = true;
            
            // Configure session for TEXT-ONLY mode (no audio)
            $this->configureSession();
            
            Log::info('Successfully connected to OpenAI Realtime API');
            return true;
            
        } catch (Exception $e) {
            Log::error('OpenAI Realtime connection failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create WebSocket connection with proper handshake
     */
    private function createWebSocketConnection($url, $headers)
    {
        $urlParts = parse_url($url);
        $host = $urlParts['host'];
        $port = 443; // WSS port
        $path = $urlParts['path'] . '?' . $urlParts['query'];
        
        // Create SSL connection
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]);
        
        $socket = stream_socket_client(
            "ssl://{$host}:{$port}",
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );
        
        if (!$socket) {
            throw new Exception("Socket connection failed: {$errstr} ({$errno})");
        }
        
        // WebSocket handshake
        $key = base64_encode(random_bytes(16));
        
        $handshake = "GET {$path} HTTP/1.1\r\n";
        $handshake .= "Host: {$host}\r\n";
        $handshake .= "Upgrade: websocket\r\n";
        $handshake .= "Connection: Upgrade\r\n";
        $handshake .= "Sec-WebSocket-Key: {$key}\r\n";
        $handshake .= "Sec-WebSocket-Version: 13\r\n";
        $handshake .= "Authorization: Bearer {$this->apiKey}\r\n";
        $handshake .= "OpenAI-Beta: realtime=v1\r\n";
        $handshake .= "\r\n";
        
        // Send handshake
        fwrite($socket, $handshake);
        
        // Read response
        $response = '';
        while (strpos($response, "\r\n\r\n") === false) {
            $response .= fread($socket, 1);
        }
        
        // Verify handshake
        if (strpos($response, '101 Switching Protocols') === false) {
            fclose($socket);
            throw new Exception("WebSocket handshake failed: {$response}");
        }
        
        return $socket;
    }
    
    /**
     * Configure session for text-only conversation
     */
    private function configureSession()
    {
        $sessionConfig = [
            'type' => 'session.update',
            'session' => [
                'modalities' => ['text'], // TEXT ONLY - no audio
                'instructions' => 'You are a helpful nutritionist creating meal plans. Always respond with valid JSON format for meal planning.',
                // DON'T include voice/audio fields for text-only mode
                'tools' => [],
                'tool_choice' => 'auto',
                'temperature' => 0.7,
                'max_response_output_tokens' => 1500
            ]
        ];
        
        $this->sendEvent($sessionConfig);
        
        // Wait for session.updated confirmation
        $this->waitForEvent('session.updated', 10);
    }
    
    /**
     * Send event to OpenAI Realtime API
     */
    public function sendEvent($event)
    {
        if (!$this->isConnected) {
            throw new Exception('WebSocket not connected');
        }
        
        $data = json_encode($event);
        $frame = $this->createWebSocketFrame($data);
        
        if (fwrite($this->websocket, $frame) === false) {
            throw new Exception('Failed to send WebSocket frame');
        }
        
        Log::debug('Sent event: ' . $event['type']);
    }
    
    /**
     * Create WebSocket frame for sending data
     */
    private function createWebSocketFrame($data)
    {
        $length = strlen($data);
        $frame = chr(0x81); // Text frame with FIN bit set
        
        // Payload length
        if ($length < 126) {
            $frame .= chr($length | 0x80); // Set mask bit
        } elseif ($length < 65536) {
            $frame .= chr(126 | 0x80);
            $frame .= pack('n', $length);
        } else {
            $frame .= chr(127 | 0x80);
            $frame .= pack('J', $length);
        }
        
        // Masking key (required for client-to-server)
        $mask = random_bytes(4);
        $frame .= $mask;
        
        // Apply mask to data
        for ($i = 0; $i < $length; $i++) {
            $frame .= $data[$i] ^ $mask[$i % 4];
        }
        
        return $frame;
    }
    
    /**
     * Generate meal plan using Realtime API
     */
    public function generateMealPlan($prompt)
    {
        try {
            Log::info('Generating meal plan via Realtime API...');
            
            // Send conversation item (user message)
            $this->sendEvent([
                'type' => 'conversation.item.create',
                'item' => [
                    'type' => 'message',
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'input_text',
                            'text' => $prompt
                        ]
                    ]
                ]
            ]);
            
            // Request response generation
            $this->sendEvent([
                'type' => 'response.create',
                'response' => [
                    'modalities' => ['text'],
                    'instructions' => 'Respond only with valid JSON for the meal plan.'
                ]
            ]);
            
            // Wait for complete response
            $response = $this->waitForCompleteResponse();
            
            Log::info('Meal plan generated successfully');
            return $response;
            
        } catch (Exception $e) {
            Log::error('Meal plan generation failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Wait for specific event type
     */
    private function waitForEvent($eventType, $timeout = 30)
    {
        $startTime = time();
        
        while (time() - $startTime < $timeout) {
            $event = $this->receiveEvent();
            
            if ($event && isset($event['type']) && $event['type'] === $eventType) {
                return $event;
            }
            
            if ($event && isset($event['type']) && $event['type'] === 'error') {
                throw new Exception('API Error: ' . ($event['error']['message'] ?? 'Unknown error'));
            }
            
            usleep(100000); // 0.1 second
        }
        
        throw new Exception("Timeout waiting for event: {$eventType}");
    }
    
    /**
     * Wait for complete response from API
     */
    private function waitForCompleteResponse($timeout = 60)
    {
        $startTime = time();
        $responseText = '';
        $isComplete = false;
        
        while (time() - $startTime < $timeout && !$isComplete) {
            $event = $this->receiveEvent();
            
            if (!$event || !isset($event['type'])) {
                continue;
            }
            
            switch ($event['type']) {
                case 'response.text.delta':
                    // Accumulate text chunks
                    if (isset($event['delta'])) {
                        $responseText .= $event['delta'];
                    }
                    break;
                    
                case 'response.text.done':
                    // Complete text response
                    if (isset($event['text'])) {
                        $responseText = $event['text'];
                    }
                    break;
                    
                case 'response.done':
                    // Response generation complete
                    $isComplete = true;
                    
                    // Extract text from response if not already captured
                    if (empty($responseText) && isset($event['response']['output'])) {
                        foreach ($event['response']['output'] as $output) {
                            if (isset($output['content'])) {
                                foreach ($output['content'] as $content) {
                                    if ($content['type'] === 'text') {
                                        $responseText .= $content['text'];
                                    }
                                }
                            }
                        }
                    }
                    break;
                    
                case 'error':
                    throw new Exception('Response error: ' . ($event['error']['message'] ?? 'Unknown error'));
                    
                case 'response.created':
                case 'response.output_item.added':
                case 'response.content_part.added':
                    // Expected events during response generation
                    break;
                    
                default:
                    Log::debug('Unhandled event type: ' . $event['type']);
            }
        }
        
        if (!$isComplete) {
            throw new Exception('Response timeout');
        }
        
        if (empty($responseText)) {
            throw new Exception('No text response received');
        }
        
        return trim($responseText);
    }
    
    /**
     * Receive event from WebSocket
     */
    private function receiveEvent()
    {
        if (!$this->websocket) {
            return null;
        }
        
        // Read WebSocket frame
        $data = $this->readWebSocketFrame();
        
        if ($data === null) {
            return null;
        }
        
        // Parse JSON event
        try {
            return json_decode($data, true);
        } catch (Exception $e) {
            Log::warning('Failed to parse event JSON: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Read WebSocket frame from socket
     */
    private function readWebSocketFrame()
    {
        // Read frame header (minimum 2 bytes)
        $header = fread($this->websocket, 2);
        if (strlen($header) < 2) {
            return null;
        }
        
        $firstByte = ord($header[0]);
        $secondByte = ord($header[1]);
        
        // Check opcode (should be text frame = 0x1)
        $opcode = $firstByte & 0x0F;
        if ($opcode !== 0x01) {
            return null; // Not a text frame
        }
        
        // Get payload length
        $payloadLength = $secondByte & 0x7F;
        $masked = ($secondByte & 0x80) === 0x80;
        
        // Read extended payload length if needed
        if ($payloadLength === 126) {
            $extLength = fread($this->websocket, 2);
            if (strlen($extLength) < 2) return null;
            $payloadLength = unpack('n', $extLength)[1];
        } elseif ($payloadLength === 127) {
            $extLength = fread($this->websocket, 8);
            if (strlen($extLength) < 8) return null;
            $payloadLength = unpack('J', $extLength)[1];
        }
        
        // Read mask if present (server-to-client frames are not masked)
        if ($masked) {
            $mask = fread($this->websocket, 4);
            if (strlen($mask) < 4) return null;
        }
        
        // Read payload data
        $payload = '';
        $bytesRead = 0;
        while ($bytesRead < $payloadLength) {
            $chunk = fread($this->websocket, $payloadLength - $bytesRead);
            if ($chunk === false || strlen($chunk) === 0) {
                break;
            }
            $payload .= $chunk;
            $bytesRead += strlen($chunk);
        }
        
        // Unmask payload if needed
        if ($masked && isset($mask)) {
            for ($i = 0; $i < strlen($payload); $i++) {
                $payload[$i] = $payload[$i] ^ $mask[$i % 4];
            }
        }
        
        return $payload;
    }
    
    /**
     * Close WebSocket connection
     */
    public function disconnect()
    {
        if ($this->websocket && $this->isConnected) {
            // Send close frame
            $closeFrame = chr(0x88) . chr(0x80) . random_bytes(4);
            @fwrite($this->websocket, $closeFrame);
            
            fclose($this->websocket);
            $this->websocket = null;
            $this->isConnected = false;
            
            Log::info('Disconnected from OpenAI Realtime API');
        }
    }
    
    /**
     * Check if connected
     */
    public function isConnected()
    {
        return $this->isConnected && $this->websocket;
    }
}




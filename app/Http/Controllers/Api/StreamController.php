<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StreamController extends Controller
{
    /**
     * Stream meal generation progress via Server-Sent Events
     */
    public function stream($sessionId)
    {
        return new StreamedResponse(function () use ($sessionId) {
            // Set up SSE headers
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no'); // Disable Nginx buffering
            
            // Send initial connection message
            $this->sendEvent('connected', [
                'message' => 'Connected to meal generation stream',
                'session_id' => $sessionId
            ]);

            $lastStatus = null;
            $lastDay = 0;
            $retryCount = 0;
            $maxRetries = 600; // 10 minutes (600 seconds) max wait time

            while ($retryCount < $maxRetries) {
                try {
                    // Get session data
                    $session = DB::table('meal_sessions')
                        ->where('id', $sessionId)
                        ->first();

                    if (!$session) {
                        $this->sendEvent('error', [
                            'message' => 'Session not found',
                            'session_id' => $sessionId
                        ]);
                        break;
                    }

                    // Check if status changed
                    if ($session->status !== $lastStatus) {
                        $lastStatus = $session->status;
                        
                        $this->sendEvent('status', [
                            'status' => $session->status,
                            'message' => $this->getStatusMessage($session->status),
                            'progress' => $this->calculateProgress($session)
                        ]);
                    }

                    // Check if day progressed
                    if ($session->current_day > $lastDay) {
                        $lastDay = $session->current_day;
                        
                        // Send day progress event
                        $this->sendEvent('day_progress', [
                            'day' => $session->current_day,
                            'total_days' => $session->total_days,
                            'status' => 'generating',
                            'progress' => $this->calculateProgress($session)
                        ]);

                        // Get meal data for completed day
                        if ($session->meal_data) {
                            $mealData = json_decode($session->meal_data, true);
                            
                            if (isset($mealData[$lastDay - 1])) {
                                $this->sendEvent('meal_data', [
                                    'day' => $lastDay,
                                    'meals' => $mealData[$lastDay - 1],
                                    'daily_total' => $this->getDailyTotal($session, $lastDay - 1)
                                ]);
                                
                                $this->sendEvent('day_complete', [
                                    'day' => $lastDay,
                                    'progress' => $this->calculateProgress($session),
                                    'message' => "Day {$lastDay} completed successfully"
                                ]);
                            }
                        }
                    }

                    // Check if generation completed
                    if ($session->status === 'completed') {
                        $this->sendEvent('complete', [
                            'status' => 'completed',
                            'total_days' => $session->total_days,
                            'meal_plan_id' => $session->id,
                            'summary' => [
                                'total_calories' => $session->total_calories,
                                'total_protein' => $session->total_protein,
                                'total_carbs' => $session->total_carbs,
                                'total_fat' => $session->total_fat,
                                'total_price' => $session->total_price,
                                'total_meals' => $session->total_meals
                            ],
                            'message' => 'Meal generation completed successfully!'
                        ]);
                        break;
                    }

                    // Check if generation failed
                    if ($session->status === 'failed') {
                        $this->sendEvent('error', [
                            'status' => 'failed',
                            'message' => $session->error_message ?? 'Meal generation failed',
                            'session_id' => $sessionId
                        ]);
                        break;
                    }

                    // Send heartbeat to keep connection alive
                    if ($retryCount % 30 === 0) { // Every 30 seconds
                        $this->sendEvent('heartbeat', [
                            'timestamp' => time(),
                            'status' => $session->status,
                            'progress' => $this->calculateProgress($session)
                        ]);
                    }

                    // Flush output
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();

                    // Wait before next check
                    sleep(1);
                    $retryCount++;

                } catch (\Exception $e) {
                    Log::error('Stream Error: ' . $e->getMessage());
                    
                    $this->sendEvent('error', [
                        'message' => 'Stream error occurred',
                        'error' => config('app.debug') ? $e->getMessage() : 'Internal error'
                    ]);
                    break;
                }
            }

            // Timeout reached
            if ($retryCount >= $maxRetries) {
                $this->sendEvent('timeout', [
                    'message' => 'Stream timeout reached. Please reconnect.',
                    'session_id' => $sessionId
                ]);
            }

        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Send SSE event
     */
    private function sendEvent($event, $data)
    {
        echo "event: {$event}\n";
        echo "data: " . json_encode($data) . "\n\n";
        
        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
    }

    /**
     * Calculate progress percentage
     */
    private function calculateProgress($session)
    {
        if ($session->total_days == 0) {
            return 0;
        }

        return round(($session->current_day / $session->total_days) * 100, 2);
    }

    /**
     * Get status message
     */
    private function getStatusMessage($status)
    {
        return match($status) {
            'pending' => 'Preparing meal generation...',
            'processing' => 'Generating your personalized meal plan...',
            'completed' => 'Meal plan generation completed!',
            'failed' => 'Meal generation failed',
            default => 'Processing...'
        };
    }

    /**
     * Get daily total from session
     */
    private function getDailyTotal($session, $dayIndex)
    {
        if (!$session->daily_totals) {
            return null;
        }

        $dailyTotals = json_decode($session->daily_totals, true);
        
        return $dailyTotals[$dayIndex] ?? null;
    }
}
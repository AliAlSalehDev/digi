<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\Ingredient;
use App\Models\Sauce;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class WebSocketMealController extends Controller implements MessageComponentInterface
{
    protected $clients;
    protected $conversations; // Store conversation history per client

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->conversations = [];
    }

    // WebSocket connection opened
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $this->conversations[$conn->resourceId] = [
            'messages' => [],
            'current_day' => 1,
            'goal_decision' => null,
            'goal_explanation' => null,
            'meal_plan' => [],
            'user_info' => null
        ];

        Log::info("New connection! ({$conn->resourceId})");
        
        $conn->send(json_encode([
            'type' => 'connected',
            'message' => 'Connected to meal planner',
            'client_id' => $conn->resourceId
        ]));
    }

    // WebSocket message received
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        
        switch ($data['type']) {
            case 'start_meal_generation':
                $this->startMealGeneration($from, $data);
                break;
            case 'generate_next_day':
                $this->generateNextDay($from);
                break;
            case 'reset_conversation':
                $this->resetConversation($from);
                break;
        }
    }

    // WebSocket connection closed
    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        unset($this->conversations[$conn->resourceId]);
        Log::info("Connection {$conn->resourceId} has disconnected");
    }

    // WebSocket error
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        Log::error("WebSocket error: " . $e->getMessage());
        $conn->close();
    }

    // Start the meal generation process
    private function startMealGeneration(ConnectionInterface $conn, $data)
    {
        try {
            $clientId = $conn->resourceId;
            $userInfo = $data['user_info'] ?? null;
            
            if (!$userInfo) {
                $conn->send(json_encode([
                    'type' => 'error',
                    'message' => 'User information required to start meal generation'
                ]));
                return;
            }

            // Store user info in conversation
            $this->conversations[$clientId]['user_info'] = $userInfo;
            
            // Send initial status
            $conn->send(json_encode([
                'type' => 'generation_started',
                'message' => 'Starting 30-day meal plan generation...',
                'total_days' => 30
            ]));

            // Start generating from day 1
            $this->generateDay($conn, 1);

        } catch (\Exception $e) {
            Log::error('Error starting meal generation: ' . $e->getMessage());
            $conn->send(json_encode([
                'type' => 'error',
                'message' => 'Failed to start meal generation: ' . $e->getMessage()
            ]));
        }
    }

    // Generate specific day with conversation context
    private function generateDay(ConnectionInterface $conn, $day)
    {
        try {
            $clientId = $conn->resourceId;
            $conversation = $this->conversations[$clientId];
            
            // Send progress update
            $conn->send(json_encode([
                'type' => 'progress_update',
                'current_day' => $day,
                'progress' => round(($day / 30) * 100, 1),
                'message' => "Generating Day {$day}..."
            ]));

            // Build prompt with conversation context
            $prompt = $this->buildPromptWithContext($conversation, $day);
            
            // Add user message to conversation
            $this->conversations[$clientId]['messages'][] = [
                'role' => 'user',
                'content' => $prompt,
                'timestamp' => now(),
                'day' => $day
            ];

            // Generate meal plan using OpenAI
            $response = $this->callOpenAI($this->conversations[$clientId]['messages']);
            
            // Add AI response to conversation
            $this->conversations[$clientId]['messages'][] = [
                'role' => 'assistant',
                'content' => $response,
                'timestamp' => now(),
                'day' => $day
            ];

            // Process response
            $jsonData = json_decode($response, true);
            if (!$jsonData) {
                throw new \Exception("Invalid JSON response from AI for day {$day}");
            }

            // Store goal info (day 1 only)
            if ($day === 1) {
                if (isset($jsonData['goal_decision'])) {
                    $this->conversations[$clientId]['goal_decision'] = $jsonData['goal_decision'];
                }
                if (isset($jsonData['goal_explanation'])) {
                    $this->conversations[$clientId]['goal_explanation'] = $jsonData['goal_explanation'];
                }
            }

            // Store meal plan
            if (isset($jsonData['day_meal_plan'])) {
                $this->conversations[$clientId]['meal_plan'][] = $jsonData['day_meal_plan'];
            }

            // Update current day
            $this->conversations[$clientId]['current_day'] = $day + 1;

            // Send day completion update
            $conn->send(json_encode([
                'type' => 'day_completed',
                'day' => $day,
                'meal_plan' => $jsonData['day_meal_plan'] ?? null,
                'goal_decision' => $this->conversations[$clientId]['goal_decision'],
                'goal_explanation' => $this->conversations[$clientId]['goal_explanation'],
                'total_meal_plan' => $this->conversations[$clientId]['meal_plan'],
                'progress' => round(($day / 30) * 100, 1)
            ]));

            // Continue to next day or finish
            if ($day < 30) {
                // Schedule next day generation after a short delay
                $this->scheduleNextDay($conn, $day + 1);
            } else {
                // All days completed
                $conn->send(json_encode([
                    'type' => 'generation_completed',
                    'message' => 'All 30 days completed!',
                    'final_meal_plan' => $this->conversations[$clientId]['meal_plan'],
                    'goal_decision' => $this->conversations[$clientId]['goal_decision'],
                    'goal_explanation' => $this->conversations[$clientId]['goal_explanation']
                ]));
            }

        } catch (\Exception $e) {
            Log::error("Error generating day {$day}: " . $e->getMessage());
            $conn->send(json_encode([
                'type' => 'day_error',
                'day' => $day,
                'message' => "Error generating day {$day}: " . $e->getMessage()
            ]));
        }
    }

    // Schedule next day generation
    private function scheduleNextDay(ConnectionInterface $conn, $nextDay)
    {
        // Use a timer or immediate call based on preference
        // For now, we'll call immediately but you can add delay
        $this->generateDay($conn, $nextDay);
    }

    // Build prompt with full conversation context
    private function buildPromptWithContext($conversation, $day)
    {
        $userInfo = $conversation['user_info'];
        $goalDecision = $conversation['goal_decision'];
        
        // Get ingredients and sauces
        $ingredients = Ingredient::pluck('name')->toArray();
        $sauces = Sauce::pluck('name')->toArray();

        if ($day === 1) {
            // Day 1: Set the foundation
            return "You are a professional nutritionist creating a comprehensive 30-day meal plan. This is the beginning of our conversation.

ANALYZE THE USER'S PROFILE:
- Age: {$userInfo['age']} years
- Height: {$userInfo['height']} meters  
- Weight: {$userInfo['weight']} kg
- Gender: {$userInfo['gender']}
- Activity Level: {$userInfo['activity_level']}
- BMI: {$userInfo['bmi']} ({$userInfo['bmi_overview']})
- BMR: {$userInfo['bmr']} calories
- TDEE: {$userInfo['tdee']} calories
- Body Fat: {$userInfo['body_fat']}%

AVAILABLE INGREDIENTS: " . implode(', ', array_slice($ingredients, 0, 50)) . "
AVAILABLE SAUCES: " . implode(', ', array_slice($sauces, 0, 20)) . "

TASK: 
1. Determine if this user should LOSE, GAIN, or MAINTAIN weight based on their profile
2. Create Day 1 meal plan with 3-5 meals appropriate for their goal
3. Use ONLY the provided ingredients and sauces
4. This is Day 1 of 30 - establish a foundation we'll build upon

Remember: We will continue this conversation for 29 more days, so maintain consistency in your recommendations.

JSON FORMAT:
{
    \"goal_decision\": \"lose/gain/maintain\",
    \"goal_explanation\": \"Detailed explanation of why you chose this goal\",
    \"day_meal_plan\": {
        \"day\": 1,
        \"meals\": [
            {
                \"meal_type\": \"breakfast\",
                \"meal_name\": \"Meal Name\",
                \"ingredients\": [{\"name\": \"ingredient\", \"amount\": \"100g\", \"calories\": 150, \"protein\": 20, \"carbs\": 10, \"fat\": 5}],
                \"sauces\": [{\"name\": \"sauce\", \"amount\": \"1 tbsp\", \"calories\": 20, \"protein\": 0, \"carbs\": 5, \"fat\": 0}],
                \"cooking_instructions\": \"Step-by-step instructions\",
                \"total_calories\": 170,
                \"total_protein\": 20,
                \"total_carbs\": 15,
                \"total_fat\": 5,
                \"meal_time\": \"07:00\"
            }
        ]
    }
}";
        } else {
            // Day 2-30: Continue the conversation
            $previousDays = array_slice($conversation['meal_plan'], -3); // Last 3 days for context
            $previousMealsContext = "";
            
            foreach ($previousDays as $dayPlan) {
                $previousMealsContext .= "Day {$dayPlan['day']}: ";
                foreach ($dayPlan['meals'] as $meal) {
                    $previousMealsContext .= "{$meal['meal_type']} ({$meal['meal_name']}), ";
                }
                $previousMealsContext = rtrim($previousMealsContext, ', ') . ". ";
            }

            return "Continue our 30-day meal plan conversation. You've established the goal: {$goalDecision} weight.

RECENT MEAL HISTORY:
{$previousMealsContext}

CREATE DAY {$day}:
- Follow the {$goalDecision} weight goal we established
- Ensure variety from recent days  
- Use same ingredients/sauces as before
- Maintain nutritional consistency
- Create 3-5 meals as established

This is our ongoing conversation - maintain the same standards and approach.

JSON FORMAT:
{
    \"day_meal_plan\": {
        \"day\": {$day},
        \"meals\": [
            // Same structure as Day 1
        ]
    }
}";
        }
    }

    // Call OpenAI with conversation history
    private function callOpenAI($messages)
    {
        try {
            // Prepare messages for OpenAI (only keep recent context to manage tokens)
            $apiMessages = [
                [
                    'role' => 'system',
                    'content' => 'You are a professional nutritionist maintaining an ongoing conversation to create a 30-day meal plan. Always respond with valid JSON only. Maintain consistency throughout our conversation.'
                ]
            ];

            // Add conversation messages (keep last 10 exchanges)
            $recentMessages = array_slice($messages, -10);
            foreach ($recentMessages as $msg) {
                $apiMessages[] = [
                    'role' => $msg['role'],
                    'content' => $msg['content']
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('AI_API_KEY'),
                'Content-Type' => 'application/json'
            ])->timeout(120)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4',
                'messages' => $apiMessages,
                'max_tokens' => 2000,
                'temperature' => 0.7
            ]);

            if (!$response->successful()) {
                throw new \Exception('OpenAI API request failed: ' . $response->body());
            }

            $data = $response->json();
            
            if (!isset($data['choices'][0]['message']['content'])) {
                throw new \Exception('Invalid OpenAI response structure');
            }

            return trim($data['choices'][0]['message']['content']);

        } catch (\Exception $e) {
            Log::error('OpenAI API error: ' . $e->getMessage());
            throw new \Exception('AI service unavailable: ' . $e->getMessage());
        }
    }

    // Generate next day (called via WebSocket)
    private function generateNextDay(ConnectionInterface $conn)
    {
        $clientId = $conn->resourceId;
        $currentDay = $this->conversations[$clientId]['current_day'];
        
        if ($currentDay <= 30) {
            $this->generateDay($conn, $currentDay);
        } else {
            $conn->send(json_encode([
                'type' => 'error',
                'message' => 'All 30 days have been completed'
            ]));
        }
    }

    // Reset conversation
    private function resetConversation(ConnectionInterface $conn)
    {
        $clientId = $conn->resourceId;
        $this->conversations[$clientId] = [
            'messages' => [],
            'current_day' => 1,
            'goal_decision' => null,
            'goal_explanation' => null,
            'meal_plan' => [],
            'user_info' => $this->conversations[$clientId]['user_info'] // Keep user info
        ];

        $conn->send(json_encode([
            'type' => 'conversation_reset',
            'message' => 'Conversation reset successfully'
        ]));
    }

    // HTTP endpoint to start WebSocket server
    public function startWebSocketServer()
    {
        $app = new \Ratchet\App('localhost', 8080);
        $app->route('/meal-planner', new WebSocketMealController);
        $app->run();
    }
}

// Separate controller for HTTP routes
class MealPlannerController extends Controller
{
    public function saveUserInfo(Request $request)
    {
        $validatedData = $request->validate([
            'age' => 'required|numeric',
            'height' => 'required|numeric',
            'weight' => 'required|numeric',
            'gender' => 'required|in:male,female',
            'activity_level' => 'required',
            'waist_circumference' => 'required|numeric',
            'neck_circumference' => 'required|numeric',
            'hip_circumference' => 'required_if:gender,female|nullable|numeric',
        ]);

        // Calculate BMI, BMR, TDEE, Body Fat (same as before)
        $bmi = $request->weight / ($request->height * $request->height);
        $bmiOverview = match (true) {
            $bmi < 18.5 => 'Underweight',
            $bmi < 24.9 => 'Normal',
            $bmi < 29.9 => 'Overweight',
            default => 'Obese',
        };

        $heightInCm = $request->height * 100;
        if ($request->gender === 'male') {
            $bmr = 10 * $request->weight + 6.25 * $heightInCm - 5 * $request->age + 5;
            $waist = $request->waist_circumference;
            $neck = $request->neck_circumference;
            $heightInInches = $request->height * 39.3701;
            $bodyFat = 86.010 * log10($waist - $neck) - 70.041 * log10($heightInInches) + 36.76;
        } else {
            $bmr = 10 * $request->weight + 6.25 * $heightInCm - 5 * $request->age - 161;
            $waist = $request->waist_circumference;
            $neck = $request->neck_circumference;
            $hip = $request->hip_circumference ?? 0;
            $heightInInches = $request->height * 39.3701;
            $bodyFat = 163.205 * log10($waist + $hip - $neck) - 97.684 * log10($heightInInches) - 78.387;
        }

        $activityFactors = [
            "Sedentary (little or no exercise)" => 1.2,
            "Lightly active (1–3 days/week)" => 1.375,
            "Moderately active (3–5 days/week)" => 1.55,
            "Very active (6–7 days/week)" => 1.725,
            "Super active (twice/day or physical job)" => 1.9,
        ];

        $activityFactor = $activityFactors[$request->activity_level] ?? 1.2;
        $tdee = $bmr * $activityFactor;

        // Store in session
        session([
            'user-info' => [
                'age' => $request->age,
                'height' => $request->height,
                'weight' => $request->weight,
                'gender' => $request->gender,
                'activity_level' => $request->activity_level,
                'neck_circumference' => $request->neck_circumference,
                'waist_circumference' => $request->waist_circumference,
                'hip_circumference' => $request->hip_circumference,
                'bmi' => round($bmi, 2),
                'bmi_overview' => $bmiOverview,
                'bmr' => round($bmr),
                'tdee' => round($tdee),
                'body_fat' => round($bodyFat, 2),
            ]
        ]);

        return redirect('generate-meal');
    }

    public function generateMeal()
    {
        $userInfo = session('user-info');
        if (!$userInfo) {
            return redirect('/');
        }

        return view('website.pages.generate-meal-websocket', compact('userInfo'));
    }

    public function approveMeal()
    {
        Session::forget('user-info');
        return redirect('/')->with('success', 'Meal plan completed successfully');
    }
}
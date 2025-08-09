<?php

namespace App\Services;

use App\Models\MealSession;
use App\Models\User;
use App\Jobs\GenerateMealPlanJob;
use App\Services\RealtimeAIService;
use App\Models\Ingredient;
use App\Models\Sauce;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MealGenerationService
{
    protected $realtimeAI;

    public function __construct()
    {
        $this->realtimeAI = new RealtimeAIService();
    }

    /**
     * Create a new meal generation session
     */
    public function createSession(User $user, $planPeriod = 30)
    {
        $session = MealSession::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'status' => 'pending',
            'current_day' => 0,
            'total_days' => $planPeriod,
            'goal' => $user->goal,
            'started_at' => now()
        ]);

        // Increment user's plan counter
        $user->increment('total_plans_generated');

        return $session;
    }

    /**
     * Start meal generation (dispatch to queue or process directly)
     */
    public function startGeneration(MealSession $session)
    {
        try {
            // Update session status
            $session->update(['status' => 'processing']);

            // For immediate processing (without queue)
            // You can change this to dispatch to queue for better performance
            if (config('queue.default') === 'sync') {
                // Process synchronously
                $this->processMealGeneration($session);
            } else {
                // Dispatch to queue
                GenerateMealPlanJob::dispatch($session);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to start meal generation: ' . $e->getMessage());
            $session->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Process meal generation for all days
     */
    public function processMealGeneration(MealSession $session)
    {
        try {
            $user = $session->user;
            $conversationHistory = [];
            $allMeals = [];
            $dailyTotals = [];
            $totalCalories = 0;
            $totalProtein = 0;
            $totalCarbs = 0;
            $totalFat = 0;
            $totalPrice = 0;
            $totalMealsCount = 0;

            // Try to connect to Realtime API
            $useRealtime = false;
            if ($this->realtimeAI->connect()) {
                $useRealtime = true;
                Log::info("Connected to OpenAI Realtime API for session {$session->id}");
            } else {
                Log::info("Using regular API for session {$session->id}");
            }

            for ($day = 1; $day <= $session->total_days; $day++) {
                try {
                    // Update current day
                    $session->update(['current_day' => $day]);

                    // Generate prompt for current day
                    $prompt = $this->generatePrompt($user, $day, $conversationHistory);

                    // Get AI response
                    $response = null;
                    if ($useRealtime && $this->realtimeAI->isConnected()) {
                        try {
                            $response = $this->realtimeAI->generateMealPlan($prompt);
                        } catch (\Exception $e) {
                            Log::warning("Realtime API failed, falling back to regular API: " . $e->getMessage());
                            $useRealtime = false;
                        }
                    }

                    if (!$response) {
                        // Use regular API
                        $response = $this->generateWithRegularAPI($prompt, $conversationHistory);
                    }

                    // Parse response
                    $mealData = $this->parseAIResponse($response);

                    if (!$mealData) {
                        throw new \Exception("Invalid meal data for day {$day}");
                    }

                    // Store goal information (first day only)
                    if ($day === 1 && isset($mealData['goal'])) {
                        $session->update([
                            'goal' => $mealData['goal'],
                            'goal_explanation' => $mealData['goal_explanation'] ?? null
                        ]);
                    }

                    // Add meals to collection
                    if (isset($mealData['meals'])) {
                        $allMeals[] = $mealData['meals'];
                        
                        // Calculate daily totals
                        $dayTotal = $this->calculateDayTotal($mealData['meals']);
                        $dailyTotals[] = $dayTotal;

                        // Update running totals
                        $totalCalories += $dayTotal['calories'];
                        $totalProtein += $dayTotal['protein'];
                        $totalCarbs += $dayTotal['carbs'];
                        $totalFat += $dayTotal['fat'];
                        $totalPrice += $dayTotal['price'];
                        $totalMealsCount += count($mealData['meals']);
                    }

                    // Update session with progress
                    $session->update([
                        'meal_data' => json_encode($allMeals),
                        'daily_totals' => json_encode($dailyTotals),
                        'total_calories' => $totalCalories,
                        'total_protein' => $totalProtein,
                        'total_carbs' => $totalCarbs,
                        'total_fat' => $totalFat,
                        'total_price' => $totalPrice,
                        'total_meals' => $totalMealsCount
                    ]);

                    // Add to conversation history
                    $conversationHistory[] = ['role' => 'user', 'content' => $prompt];
                    $conversationHistory[] = ['role' => 'assistant', 'content' => $response];

                    // Keep conversation history manageable
                    if (count($conversationHistory) > 10) {
                        $conversationHistory = array_slice($conversationHistory, -10);
                    }

                    Log::info("Completed day {$day} for session {$session->id}");

                    // Small delay between days to avoid rate limiting
                    if ($day < $session->total_days) {
                        sleep(1);
                    }

                } catch (\Exception $e) {
                    Log::error("Error generating day {$day}: " . $e->getMessage());
                    
                    // Try to continue with next day
                    if ($day < 3) {
                        // If early days fail, mark as failed
                        throw $e;
                    }
                    // Otherwise, continue with remaining days
                }
            }

            // Mark as completed
            $session->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

            Log::info("Meal generation completed for session {$session->id}");

        } catch (\Exception $e) {
            Log::error("Meal generation failed for session {$session->id}: " . $e->getMessage());
            
            $session->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            throw $e;
        } finally {
            // Disconnect Realtime API if connected
            if ($useRealtime) {
                $this->realtimeAI->disconnect();
            }
        }
    }

    /**
     * Generate prompt for AI
     */
    private function generatePrompt(User $user, $day, $conversationHistory = [])
    {
        // Get ingredients and sauces
        $ingredients = Ingredient::select('name', 'calories', 'protein', 'carbs', 'fats_per_100g', 'price')
            ->limit(100)
            ->get();

        $sauces = Sauce::select('name', 'calories', 'protein', 'carbs', 'fats_per_100g', 'price')
            ->limit(30)
            ->get();

        // Format ingredients and sauces for prompt
        $ingredientList = [];
        foreach ($ingredients as $ing) {
            $ingredientList[] = "{$ing->name}|{$ing->calories}cal|{$ing->protein}p|{$ing->carbs}c|{$ing->fats_per_100g}f|{$ing->price}$";
        }

        $sauceList = [];
        foreach ($sauces as $sauce) {
            $sauceList[] = "{$sauce->name}|{$sauce->calories}cal|{$sauce->protein}p|{$sauce->carbs}c|{$sauce->fats_per_100g}f|{$sauce->price}$";
        }

        // Determine meal count based on BMI
        $mealCount = $this->getMealCountByBMI($user->bmi);

        // Build prompt
        $prompt = "Nutritionist Day {$day}. User: {$user->gender}, {$user->weight}kg, BMI {$user->bmi} ({$user->bmi_overview}), TDEE {$user->tdee}

INGREDIENTS: " . implode(' | ', $ingredientList) . "

SAUCES: " . implode(' | ', $sauceList) . "

RULES:
1. Use EXACT names from lists above
2. Create {$mealCount} meals for {$user->goal} weight goal
3. Never repeat ingredient/sauce in same day
4. Target calories: " . $this->getTargetCalories($user) . "
5. Vary meals from previous days

JSON FORMAT ONLY:
{
    \"goal\": \"{$user->goal}\",
    \"day\": {$day},
    \"meals\": [
        {
            \"type\": \"breakfast/lunch/dinner/snack\",
            \"name\": \"Meal Name\",
            \"time\": \"07:00\",
            \"ingredients\": [{\"name\": \"exact_name\", \"amount\": \"100g\", \"cal\": 150, \"protein\": 20, \"carbs\": 10, \"fat\": 5, \"price\": 5}],
            \"sauces\": [{\"name\": \"exact_name\", \"amount\": \"1tbsp\", \"cal\": 20, \"protein\": 0, \"carbs\": 5, \"fat\": 0, \"price\": 1}],
            \"instructions\": \"Preparation steps\",
            \"total_cal\": 170,
            \"total_protein\": 20,
            \"total_carbs\": 15,
            \"total_fat\": 5,
            \"total_price\": 6
        }
    ],
    \"daily_total\": {
        \"calories\": 1800,
        \"protein\": 120,
        \"carbs\": 180,
        \"fat\": 60,
        \"price\": 30
    }
}";

        return $prompt;
    }

    /**
     * Generate meal plan using regular OpenAI API
     */
    private function generateWithRegularAPI($prompt, $conversationHistory)
    {
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are a professional nutritionist. Always respond with valid JSON only.'
            ]
        ];

        // Add limited conversation history
        if (!empty($conversationHistory)) {
            $recentHistory = array_slice($conversationHistory, -4);
            foreach ($recentHistory as $msg) {
                $messages[] = $msg;
            }
        }

        $messages[] = ['role' => 'user', 'content' => $prompt];

        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . env('AI_API_KEY'),
            'Content-Type' => 'application/json'
        ])->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4',
            'messages' => $messages,
            'max_tokens' => 1500,
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
    }

    /**
     * Parse AI response
     */
    private function parseAIResponse($response)
    {
        // Clean response (remove markdown if present)
        $response = preg_replace('/^```json\s*/m', '', $response);
        $response = preg_replace('/\s*```\s*$/m', '', $response);
        $response = trim($response);

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON parse error: ' . json_last_error_msg());
            Log::error('Raw response: ' . $response);
            return null;
        }

        return $data;
    }

    /**
     * Calculate daily total from meals
     */
    private function calculateDayTotal($meals)
    {
        $total = [
            'calories' => 0,
            'protein' => 0,
            'carbs' => 0,
            'fat' => 0,
            'price' => 0
        ];

        foreach ($meals as $meal) {
            $total['calories'] += $meal['total_cal'] ?? 0;
            $total['protein'] += $meal['total_protein'] ?? 0;
            $total['carbs'] += $meal['total_carbs'] ?? 0;
            $total['fat'] += $meal['total_fat'] ?? 0;
            $total['price'] += $meal['total_price'] ?? 0;
        }

        return $total;
    }

    /**
     * Get meal count based on BMI
     */
    private function getMealCountByBMI($bmi)
    {
        if ($bmi < 18.5) {
            return '4-6'; // Underweight - more meals
        } elseif ($bmi >= 18.5 && $bmi < 25) {
            return '3-4'; // Normal weight
        } else {
            return '2-3'; // Overweight/Obese - fewer, larger meals
        }
    }

    /**
     * Get target calories based on user goal
     */
    private function getTargetCalories(User $user)
    {
        $tdee = $user->tdee;

        switch ($user->goal) {
            case 'lose':
                return $tdee - 400; // Deficit for weight loss
            case 'gain':
                return $tdee + 400; // Surplus for weight gain
            default:
                return $tdee; // Maintenance
        }
    }
}
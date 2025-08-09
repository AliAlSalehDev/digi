<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\Sauce;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Services\RealtimeAIService;
use PDF;

class WebController extends Controller
{
    public function __construct()
    {
        ini_set('max_execution_time', 1800);
    }

    public function index()
    {
        $this->resetMealPlan();
        return view('website.pages.index');
    }

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
            'plan_period' => 'nullable|in:7,30',
        ]);

        // BMI Calculation
        $heightInMeters = $request->height / 100;
        $bmi = $request->weight / ($heightInMeters * $heightInMeters);
        $bmiOverview = match (true) {
            $bmi < 18.5 => 'Underweight',
            $bmi < 24.9 => 'Normal',
            $bmi < 29.9 => 'Overweight',
            default => 'Obese',
        };

        // BMR Calculation
        $heightInCm = $request->height * 100;
        if ($request->gender === 'male') {
            $bmr = 10 * $request->weight + 6.25 * $heightInCm - 5 * $request->age + 5;

            // Body Fat % for Male
            $waist = $request->waist_circumference;
            $neck = $request->neck_circumference;
            $heightInInches = $request->height * 39.3701;
            $bodyFat = 86.010 * log10($waist - $neck) - 70.041 * log10($heightInInches) + 36.76;
        } else {
            $bmr = 10 * $request->weight + 6.25 * $heightInCm - 5 * $request->age - 161;

            // Body Fat % for Female
            $waist = $request->waist_circumference;
            $neck = $request->neck_circumference;
            $hip = $request->hip_circumference ?? 0;
            $heightInInches = $request->height * 39.3701;
            $bodyFat = 163.205 * log10($waist + $hip - $neck) - 97.684 * log10($heightInInches) - 78.387;
        }

        $bodyFat = round($bodyFat, 2);

        // Activity Factor mapping
        $activityFactors = [
            "Sedentary (little or no exercise)" => 1.2,
            "Lightly active (1–3 days/week)" => 1.375,
            "Moderately active (3–5 days/week)" => 1.55,
            "Very active (6–7 days/week)" => 1.725,
            "Super active (twice/day or physical job)" => 1.9,
        ];

        $activityFactor = $activityFactors[$request->activity_level] ?? 1.2;
        $tdee = $bmr * $activityFactor;

        // Store in session and initialize conversation
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
                'body_fat' => $bodyFat,
            ],
            'conversation_history' => [], // Initialize conversation history
            'current_day' => 1, // Track current day
            'goal' => null,
            'plan_period' => $request->plan_period,
            'goal_explanation' => null,
            'meals' => [] // Store accumulated meal plans
        ]);

        return redirect('generate-meal');
    }

    public function generateMeal()
    {
        $userInfo = session('user-info');
        if (!$userInfo) {
            return redirect('/');
        }

        // Get existing meal data
        $meals = session('meals', []);
        $goal = session('goal');

        $jsonData = [
            'goal' => $goal,
            'meals' => $meals
        ];

        return view('website.pages.generate-meal', compact('jsonData', 'userInfo'));
    }

    public function downloadpdf(Request $request)
    {
        // 'conversation_history', 'current_day', 'goal', 'goal_explanation', 'daily_total',
        // 'meals', 'total_price', 'total_calories', 'total_meals', 'current_day', 'user-info'
        try {
            $mealPlanData = session('meals');
            $userInfo = session('user-info');
            $currentDay = session('current_day', 1);
            $goalDecision = session('goal');
            $goalExplanation = session('goal_explanation');
            // Check if meal plan data exists
            if (!$mealPlanData) {
                return redirect()->back()->with('error', 'No meal plan data found. Please generate a meal plan first.');
            }

            // Prepare data for PDF
            $data = [
                'meal_plan' => $mealPlanData,
                'user_info' => $userInfo,
                'current_day' => $currentDay,
                'goal_decision' => $goalDecision,
                'goal_explanation' => $goalExplanation,
                'generated_date' => now()->format('F j, Y'),
                'total_days' => count($mealPlanData)
            ];

            // Calculate totals for summary
            $data['summary'] = $this->calculateMealPlanSummary($mealPlanData);

            // Generate PDF
            $pdf = PDF::loadView('pdf.meal-plan', $data);

            // Set paper size and orientation
            $pdf->setPaper('A4', 'portrait');

            // Set options for better rendering
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial'
            ]);

            // Download PDF with custom filename
            $filename = 'meal-plan-' . now()->format('Y-m-d') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }

    }

    private function calculateMealPlanSummary($mealPlanData)
    {
        $totalCal = 0;
        $totalProtein = 0;
        $totalCarbs = 0;
        $totalFat = 0;
        $totalPrice = 0;
        $totalMeals = 0;
        $mealTypeCount = [
            'breakfast' => 0,
            'lunch' => 0,
            'dinner' => 0,
            'snack' => 0,
            'other' => 0
        ];
        $ingredientFrequency = [];
        $dailyCalories = [];

        foreach ($mealPlanData as $dayIndex => $day) {
            $dayCalories = 0;
            $dayProtein = 0;
            $dayCarbs = 0;
            $dayFat = 0;

            foreach ($day as $meal) {
                $totalCal += $meal['total_cal'] ?? 0;
                $totalProtein += $meal['total_protein'] ?? 0;
                $totalCarbs += $meal['total_carbs'] ?? 0;
                $totalFat += $meal['total_fat'] ?? 0;
                $totalPrice += $meal['total_price'] ?? 0;
                $totalMeals++;

                $dayCalories += $meal['total_cal'] ?? 0;
                $dayProtein += $meal['total_protein'] ?? 0;
                $dayCarbs += $meal['total_carbs'] ?? 0;
                $dayFat += $meal['total_fat'] ?? 0;

                // Count meal types
                $mealType = strtolower($meal['type'] ?? 'other');
                if (isset($mealTypeCount[$mealType])) {
                    $mealTypeCount[$mealType]++;
                } else {
                    $mealTypeCount['other']++;
                }

                // Track ingredient frequency
                if (isset($meal['ingredients']) && is_array($meal['ingredients'])) {
                    foreach ($meal['ingredients'] as $ingredient) {
                        $ingredientName = $ingredient['name'] ?? 'Unknown';
                        if (!isset($ingredientFrequency[$ingredientName])) {
                            $ingredientFrequency[$ingredientName] = 0;
                        }
                        $ingredientFrequency[$ingredientName]++;
                    }
                }
            }

            $dailyCalories[] = [
                'day' => $dayIndex + 1,
                'calories' => $dayCalories,
                'protein' => $dayProtein,
                'carbs' => $dayCarbs,
                'fat' => $dayFat
            ];
        }

        // Get top 5 most used ingredients
        arsort($ingredientFrequency);
        $topIngredients = array_slice($ingredientFrequency, 0, 5, true);

        // Calculate variance and range
        $calorieValues = array_column($dailyCalories, 'calories');
        $minCalDay = !empty($calorieValues) ? min($calorieValues) : 0;
        $maxCalDay = !empty($calorieValues) ? max($calorieValues) : 0;
        $calorieRange = $maxCalDay - $minCalDay;

        $days = count($mealPlanData);

        return [
            'total_cal' => $totalCal,
            'total_protein' => $totalProtein,
            'total_carbs' => $totalCarbs,
            'total_fat' => $totalFat,
            'total_price' => $totalPrice,
            'total_meals' => $totalMeals,
            'total_days' => $days,
            'avg_cal_per_day' => $days > 0 ? $totalCal / $days : 0,
            'avg_protein_per_day' => $days > 0 ? $totalProtein / $days : 0,
            'avg_carbs_per_day' => $days > 0 ? $totalCarbs / $days : 0,
            'avg_fat_per_day' => $days > 0 ? $totalFat / $days : 0,
            'avg_price_per_day' => $days > 0 ? $totalPrice / $days : 0,
            'meal_type_distribution' => $mealTypeCount,
            'top_ingredients' => $topIngredients,
            'calorie_range' => [
                'min' => $minCalDay,
                'max' => $maxCalDay,
                'range' => $calorieRange
            ],
            'daily_breakdown' => $dailyCalories,
            'avg_meals_per_day' => $days > 0 ? $totalMeals / $days : 0
        ];
    }

    // Usage in your controller
    public function generateAiMeal(Request $request)
    {
        $realtimeAPI = null;
        if ($request->input('regenerate') === true || $request->input('regenerate') === 'true') {
            $this->resetMealPlan();
        }

        try {
            if (Ingredient::count() == 0 || Sauce::count() == 0) {
                return response()->json(['error' => true, 'message' => "No ingredients/sauces available"]);
            }

            $currentDay = session('current_day', 1);
            $maxDays = 30;

            if ($currentDay > $maxDays) {
                return response()->json(['error' => false, 'message' => "All 30 days completed!", 'completed' => true]);
            }

            // Generate prompt
            if ($request->input('meal-type') == 'our-meals') {
                $prompt = $this->ourMealPrompt($currentDay);
            } else {
                $prompt = $this->aiPrompt($currentDay);
            }


            // Try OpenAI Realtime API
            $realtimeAPI = new RealtimeAIService();
            $response = null;
            $usingRealtime = false;

            Log::info("Attempting OpenAI Realtime API for day {$currentDay}");

            if ($realtimeAPI->connect()) {
                try {
                    $response = $realtimeAPI->generateMealPlan($prompt);
                    $usingRealtime = true;
                    Log::info("✅ Successfully used OpenAI Realtime API for day {$currentDay}");
                } catch (Exception $e) {
                    Log::warning("❌ Realtime API failed for day {$currentDay}: " . $e->getMessage());
                    $realtimeAPI->disconnect();
                    $realtimeAPI = null;
                }
            }

            // Fallback to regular API if Realtime failed
            if (!$response) {
                Log::info("🔄 Falling back to regular API for day {$currentDay}");
                $response = $this->generateMealPlan($prompt, []);
                $usingRealtime = false;
            }

            Log::info("AI Response for day {$currentDay} (Realtime: " . ($usingRealtime ? 'Yes' : 'No') . "):");
            Log::info($response);

            // Validate JSON response
            $jsonData = json_decode($response, true);
            if (!$jsonData) {
                // Try to clean the response if it has markdown formatting
                $cleanedResponse = $this->cleanJsonResponse($response);
                $jsonData = json_decode($cleanedResponse, true);

                if (!$jsonData) {
                    Log::error('Raw response: ' . $response);
                    Log::error('Cleaned response: ' . $cleanedResponse);
                    throw new Exception("Invalid JSON in AI response for day {$currentDay}");
                }
            }
            if (!$jsonData) {
                throw new Exception("Invalid JSON in AI response for day {$currentDay}");
            }

            // Process response (same as your existing logic)
            if ($currentDay === 1 && isset($jsonData['goal'])) {
                session(['goal' => $jsonData['goal']]);
            }

            if (isset($jsonData['meals'])) {
                $currentMeals = session('meals', []);
                $currentMeals[] = $jsonData['meals'];
                session(['meals' => $currentMeals]);
            }

            if (isset($jsonData['daily_total'])) {
                Log::info("----");
                Log::info($jsonData['daily_total']);
                $currentDailyTotals = session('daily_total', []);
                $currentDailyTotals[] = $jsonData['daily_total'];
                session(['daily_total' => $currentDailyTotals]);
                Log::info(session('daily_total'));
                Log::info("----");
            }

            session(['current_day' => $currentDay + 1]);
            $newCalories = $jsonData['daily_total']['calories'];
            if (session('total_calories')) {
                $oldCalories = session('total_calories');
                session(['total_calories' => $oldCalories + $newCalories]);
            } else {
                session(['total_calories' => $newCalories]);
            }
            $newMeals = count($jsonData['meals']);
            if (session('total_meals')) {
                $oldMeals = session('total_meals');
                session(['total_meals' => $oldMeals + $newMeals]);
            } else {
                session(['total_meals' => $newMeals]);
            }

            $newPrice = $jsonData['daily_total']['price'];
            if (session('total_price')) {
                $oldPrice = session('total_price');
                session(['total_price' => $oldPrice + $newPrice]);
            } else {
                session(['total_price' => $newPrice]);
            }

            // Prepare final response
            $finalJson = [
                'goal' => session('goal'),
                'meals' => session('meals', []),
                'daily_total' => session('daily_total', []),
                'current_day' => $currentDay,
                'total_days' => $maxDays,
                'total_price' => session('total_price'),
                'total_calories' => session('total_calories'),
                'total_meals' => session('total_meals'),
            ];

            file_put_contents(public_path('response.json'), json_encode($finalJson, JSON_PRETTY_PRINT));

            return response()->json([
                'error' => false,
                'day_completed' => $currentDay,
                'total_days' => $maxDays,
                'progress' => round(($currentDay / $maxDays) * 100, 1),
                'realtime_used' => $usingRealtime,
                'api_type' => $usingRealtime ? 'realtime_websocket' : 'regular_http',
                'html' => view('website.pages.response', ['jsonData' => $finalJson])->render()
            ]);
        } catch (Exception $e) {
            Log::error('Error generating meal plan: ' . $e->getMessage());

            return response()->json([
                'error' => true,
                'message' => "Something went wrong: " . $e->getMessage()
            ], 500);
        } finally {
            // Always disconnect WebSocket
            if ($realtimeAPI) {
                $realtimeAPI->disconnect();
            }
        }
    }

    // Add this method to your controller
    private function cleanJsonResponse($response)
    {
        // Remove markdown code blocks
        $response = preg_replace('/^```json\s*/m', '', $response);
        $response = preg_replace('/\s*```\s*$/m', '', $response);

        // Remove any leading/trailing whitespace
        $response = trim($response);

        return $response;
    }

    public function aiPrompt($day)
    {
        $user = session('user-info');
        if (!$user) {
            throw new \Exception('User info not found in session.');
        }

        // Get only essential ingredients and sauces (limit to reduce token count)
        $ingredients = Ingredient::select('name', 'calories', 'protein', 'carbs', 'fats_per_100g', 'price')
            ->get()->toArray();

        $sauces = Sauce::select('name', 'calories', 'protein', 'carbs', 'fats_per_100g', 'price')
            ->get()->toArray();

        // Get previously used ingredients (compact format)
        $usedIngredients = [];
        $usedSauces = [];
        $existingMeals = session('meals', []);

        foreach ($existingMeals as $dayMeals) {
            foreach ($dayMeals as $meal) {
                if (isset($meal['ingredients'])) {
                    foreach ($meal['ingredients'] as $ingredient) {
                        $usedIngredients[] = $ingredient['name'];
                    }
                }
                if (isset($meal['sauces'])) {
                    foreach ($meal['sauces'] as $sauce) {
                        $usedSauces[] = $sauce['name'];
                    }
                }
            }
        }

        $usedIngredients = array_unique($usedIngredients);
        $usedSauces = array_unique($usedSauces);

        // COMPACT format - one line per ingredient/sauce
        $ingredientList = [];
        foreach ($ingredients as $ing) {
            $ingredientList[] = "{$ing['name']}|{$ing['calories']}cal|{$ing['protein']}p|{$ing['carbs']}c|{$ing['fats_per_100g']}f|{$ing['price']}$";
        }

        $sauceList = [];
        foreach ($sauces as $sauce) {
            $sauceList[] = "{$sauce['name']}|{$sauce['calories']}cal|{$sauce['protein']}p|{$sauce['carbs']}c|{$sauce['fats_per_100g']}f|{$sauce['price']}$";
        }

        $mealCount = $this->getMealCountByBMI($user['bmi']);

        // Compact used items display
        $usedText = "";
        if (!empty($usedIngredients)) {
            $usedText .= "Used: " . implode(',', array_slice($usedIngredients, 0, 20)); // Limit display
        }

        return "Nutritionist Day {$day}. User: {$user['gender']},{$user['weight']}kg,BMI {$user['bmi']},BMI Overview {$user['bmi_overview']},TDEE {$user['tdee']}
            {$usedText}

            INGREDIENTS: " . implode(' | ', $ingredientList) . "

            SAUCES: " . implode(' | ', $sauceList) . "

            RULES:
            1. Use EXACT names from lists above
            2. Use EXACT nutritional values (format: name|calories|protein|carbs|fat|price)
            3. Never repeat ingredient/sauce in same day
            4. Prefer ingredients NOT in used list
            5. Must Create {$mealCount} meals
            6. Scale portions but keep per-100g values same
            7. Every day must start with Black Coffee at 06:30 AM.
            8. Do NOT replace coffee with any other drink (e.g., tea, green tea, juice).

            JSON ONLY:
            {
            \"goal\":\"gain/lose/maintain\",
            \"day\":{$day},
            \"target_calories\":1800,
            \"meals\":[
            {
            \"type\":\"morning drink\",
            \"name\":\"Black Coffee\",
            \"time\":\"06:30\",
            \"ingredients\":[{\"name\":\"Black Coffee\",\"amount\":\"1 cup\",\"cal\":2,\"protein\":0,\"carbs\":0,\"fat\":0,\"price\":1}],
            \"instructions\":\"Serve hot without sugar.\",
            \"total_cal\":2,
            \"total_protein\":0,
            \"total_carbs\":0,
            \"total_fat\":0,
            \"total_price\":1
            },
            {
            \"type\":\"breakfast\",
            \"name\":\"Meal Name\",
            \"time\":\"07:00\",
            \"ingredients\":[{\"name\":\"EXACT_NAME\",\"amount\":\"100g\",\"cal\":165,\"protein\":31,\"carbs\":0,\"fat\":3.6,\"price\":8}],
            \"sauces\":[{\"name\":\"EXACT_NAME\",\"amount\":\"1tbsp\",\"cal\":20,\"protein\":0,\"carbs\":2,\"fat\":1,\"price\":1}],
            \"instructions\":\"Cook and serve\",
            \"total_cal\":185,
            \"total_protein\":31,
            \"total_carbs\":2,
            \"total_fat\":4.6,
            \"total_price\":9
            }
            ],
            \"daily_total\":{
            \"calories\":1802,
            \"protein\":120,
            \"carbs\":150,
            \"fat\":60,
            \"price\":36
            }
            }";
    }

    public function ourMealPrompt($day)
    {
        $user = session('user-info');
        if (!$user) {
            throw new \Exception('User info not found in session.');
        }
        $existingMeals = Meal::get();

        // Determine meal count based on BMI
        $mealCount = $this->getMealCountByBMI($user['bmi']);

        return "You are a nutritionist. Adjust the provided Day {$day} meal plan to perfectly match user's nutritional requirements.

        USER PROFILE: Age {$user['age']}, Height {$user['height']}m, Weight {$user['weight']}kg, {$user['gender']}, Activity: {$user['activity_level']}, BMI: {$user['bmi']}, TDEE: {$user['tdee']}

        EXISTING MEALS TO ADJUST: " . json_encode($existingMeals) . "

        ADJUSTMENT RULES:
        1. Adjust portion sizes to match user's TDEE requirements
        2. Maintain meal variety and balance
        3. Ensure realistic portions per meal
        4. Target {$mealCount} meals based on BMI
        5. Keep meal types appropriate for timing (breakfast, lunch, dinner, snacks)
        6. Maintain nutritional balance across the day
        7. Adjust calories to match user's goal (lose/gain/maintain weight)
        8. Preserve original meal concepts while scaling nutritional values
        9. Ensure daily totals align with user's specific needs
        10. Mention quantity how much you are using

        BMI MEAL COUNT GUIDELINES:
        - Under 18.5: 4-6 meals (weight gain focus)
        - 18.5-24.9: 3-4 meals (maintenance focus)
        - Over 25: 2-3 meals (weight loss focus)

        CALORIE GOALS:
        - Weight Loss: TDEE - 300-500 calories
        - Weight Maintenance: TDEE ± 100 calories
        - Weight Gain: TDEE + 300-500 calories

        RESPOND WITH ONLY THIS JSON (keep descriptions brief):
        {
            \"goal\": \"lose/gain/maintain\",
            \"day\": {$day},
            \"target_calories\": 0000,
            \"meals\": [
                {
                    \"type\": \"breakfast\",
                    \"name\": \"Adjusted Meal Name\",
                    \"quantity\": \"0\",
                    \"time\": \"07:00\",
                    \"description\": \"Brief meal description\",
                    \"total_cal\": 400,
                    \"total_protein\": 25,
                    \"total_carbs\": 45,
                    \"total_fat\": 15,
                    \"total_price\": 15,
                    \"portion_notes\": \"Portion adjustment notes if any\"
                }
            ],
            \"daily_total\": {
                \"calories\": 0000,
                \"protein\": 000,
                \"carbs\": 000,
                \"fat\": 000,
                \"price\": 000 ** SUM of all meal total_price**
            },
            \"adjustments_made\": \"Brief summary of key adjustments made to meet user requirements\"
        }";
    }

    private function getMealCountByBMI($bmi)
    {
        if ($bmi < 18.5) {
            return '4-6';
        } elseif ($bmi >= 18.5 && $bmi < 25) {
            return '3-4';
        } else {
            return '2-3';
        }
    }

    public function generateMealPlan(string $prompt, array $conversationHistory = []): string
    {
        try {
            $messages = [
                [
                    'role' => 'system',
                    'content' => 'You are a nutritionist. Respond with valid JSON only.'
                ]
            ];

            // Skip conversation history if prompt is long
            $promptLength = strlen($prompt);
            if ($promptLength < 2000 && !empty($conversationHistory)) {
                // Only add last 2 messages from history (1 exchange)
                $recentHistory = array_slice($conversationHistory, -2);
                foreach ($recentHistory as $message) {
                    $messages[] = $message;
                }
            }

            $messages[] = [
                'role' => 'user',
                'content' => $prompt
            ];

            // Reduce max_tokens to give more room for input
            $maxTokens = 1200;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('AI_API_KEY'),
                'Content-Type' => 'application/json'
            ])->timeout(120)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4',
                'messages' => $messages,
                'max_tokens' => $maxTokens,
                'temperature' => 0.7
            ]);

            if (!$response->successful()) {
                throw new \Exception('AI service request failed: ' . $response->body());
            }

            $data = $response->json();

            if (!isset($data['choices'][0]['message']['content'])) {
                throw new \Exception('Invalid AI response structure');
            }

            $content = trim($data['choices'][0]['message']['content']);

            // Validate JSON
            $jsonTest = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid JSON received from AI: ' . json_last_error_msg());
                throw new \Exception('AI returned invalid JSON response');
            }

            return $content;
        } catch (\Exception $e) {
            Log::error('AI service error: ' . $e->getMessage());
            throw new \Exception('AI service unavailable: ' . $e->getMessage());
        }
    }

    // Helper method to get essential conversation history
    private function getEssentialHistory(array $conversationHistory): array
    {
        if (empty($conversationHistory)) {
            return [];
        }

        $essential = [];

        // Always include the first exchange (goal setting) if it exists
        if (count($conversationHistory) >= 2) {
            $essential[] = $conversationHistory[0]; // First user message (goal setting)
            $essential[] = $conversationHistory[1]; // First AI response (goal decision)
        }

        // Include only the last 2-3 exchanges to maintain recent context
        $recentHistory = array_slice($conversationHistory, -6); // Last 3 exchanges (6 messages)

        foreach ($recentHistory as $message) {
            // Skip if already included
            if (!in_array($message, $essential)) {
                $essential[] = $message;
            }
        }

        return $essential;
    }

    // Helper method to estimate token count (rough approximation)
    private function estimateTokenCount(array $messages): int
    {
        $totalContent = '';
        foreach ($messages as $message) {
            $totalContent .= $message['content'] ?? '';
        }

        // Rough approximation: 1 token ≈ 0.75 words ≈ 4 characters
        return intval(strlen($totalContent) / 4);
    }

    // Helper method to summarize long prompts
    private function summarizePrompt(string $prompt): string
    {
        $currentDay = session('current_day', 1);
        return "Day {$currentDay} meal plan - vary ingredients, use database values exactly.";
    }

    // Helper method to summarize AI responses
    private function summarizeResponse(string $response): string
    {
        $jsonData = json_decode($response, true);
        if ($jsonData && isset($jsonData['day'])) {
            return "Day {$jsonData['day']} completed with " . count($jsonData['meals'] ?? []) . " meals.";
        }
        return "Meal plan completed.";
    }

    // Helper method to get minimal prompt when context is too long
    private function getMinimalPrompt($day): string
    {
        $user = session('user-info');
        $goal = session('goal', 'maintain');

        // Get only 15 ingredients and 8 sauces for minimal context
        $ingredients = Ingredient::select('name', 'calories', 'protein')->take(15)->get();
        $sauces = Sauce::select('name', 'calories')->take(8)->get();

        $ingList = [];
        foreach ($ingredients as $ing) {
            $ingList[] = "{$ing['name']}({$ing['calories']}cal,{$ing['protein']}p)";
        }

        $sauceList = [];
        foreach ($sauces as $sauce) {
            $sauceList[] = "{$sauce['name']}({$sauce['calories']}cal)";
        }

        return "Day {$day} meal plan. {$user['gender']}, {$user['weight']}kg, TDEE {$user['tdee']}. Goal: {$goal}.

        Ingredients: " . implode(', ', $ingList) . "
        Sauces: " . implode(', ', $sauceList) . "

        Use different ingredients. 3-4 meals. JSON:
        {\"goal\":\"{$goal}\",\"day\":{$day},\"target_calories\":1800,\"meals\":[{\"type\":\"breakfast\",\"name\":\"name\",\"time\":\"07:00\",\"ingredients\":[{\"name\":\"exact_name\",\"amount\":\"100g\",\"cal\":150,\"protein\":20,\"carbs\":10,\"fat\":5,\"price\":5}],\"sauces\":[],\"instructions\":\"cook\",\"total_cal\":150,\"total_protein\":20,\"total_carbs\":10,\"total_fat\":5,\"total_price\":5}],\"daily_total\":{\"calories\":1800,\"protein\":100,\"carbs\":150,\"fat\":60,\"price\":30}}";
    }

    private function manageConversationHistory(array $conversationHistory): array
    {
        // Keep only last 4 messages (2 exchanges) to save tokens
        if (count($conversationHistory) > 4) {
            return array_slice($conversationHistory, -4);
        }
        return $conversationHistory;
    }

    public function resetMealPlan()
    {
        Session::forget(['conversation_history', 'current_day', 'goal', 'goal_explanation', 'daily_total', 'meals', 'total_price', 'total_calories', 'total_meals', 'current_day', 'user-info']);
        session(['current_day' => 1]);

        return response()->json(['success' => true, 'message' => 'Meal plan reset successfully']);
    }

    // Add method to continue from specific day
    public function continueFromDay(Request $request)
    {
        $day = $request->input('day', 1);
        session(['current_day' => max(1, min(30, $day))]);

        return response()->json(['success' => true, 'current_day' => session('current_day')]);
    }

    public function approveMeal()
    {
        Session::forget(['conversation_history', 'current_day', 'goal', 'goal_explanation', 'daily_total', 'meals', 'total_price', 'total_calories', 'total_meals', 'current_day', 'user-info']);
        return redirect('/')->with('success', 'Meal plan completed successfully');
    }
}

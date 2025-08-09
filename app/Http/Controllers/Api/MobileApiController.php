<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserIdentificationService;
use App\Services\MealGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MobileApiController extends Controller
{
    protected $userService;
    protected $mealService;

    public function __construct(
        UserIdentificationService $userService,
        MealGenerationService $mealService
    ) {
        $this->userService = $userService;
        $this->mealService = $mealService;
    }

    /**
     * Generate meals for mobile user
     * No authentication required - users identified by physical metrics
     */
    public function generateMeals(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'device_id' => 'nullable|string|max:255',
                'age' => 'required|integer|min:1|max:120',
                'height' => 'required|numeric|min:50|max:300', // in cm
                'weight' => 'required|numeric|min:20|max:500', // in kg
                'gender' => 'required|in:male,female',
                'activity_level' => 'required|string',
                'neck_circumference' => 'required|numeric|min:10|max:100',
                'waist_circumference' => 'required|numeric|min:30|max:200',
                'hip_circumference' => 'nullable|numeric|min:30|max:200',
                'plan_period' => 'nullable|integer|in:7,30',
            ]);

            // Set default plan period
            $validated['plan_period'] = $validated['plan_period'] ?? 30;

            DB::beginTransaction();

            // Find or create user based on physical metrics
            $user = $this->userService->findOrCreateUser($validated);

            // Create meal generation session
            $session = $this->mealService->createSession($user, $validated['plan_period']);

            // Start async meal generation (will be processed in background)
            $this->mealService->startGeneration($session);

            DB::commit();

            // Return session info for streaming
            return response()->json([
                'success' => true,
                'data' => [
                    'session_id' => $session->id,
                    'stream_url' => route('api.mobile.stream', ['sessionId' => $session->id]),
                    'user_id' => $user->id,
                    'is_existing_user' => $user->wasRecentlyCreated ? false : true,
                    'user_metrics' => [
                        'bmi' => $user->bmi,
                        'bmi_overview' => $user->bmi_overview,
                        'bmr' => $user->bmr,
                        'tdee' => $user->tdee,
                        'body_fat' => $user->body_fat,
                        'goal' => $user->goal
                    ]
                ],
                'message' => 'Meal generation started successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Mobile API Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to start meal generation',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get existing meal plan for user
     */
    public function getMealPlan(Request $request, $userIdentifier)
    {
        try {
            // Find user by hash or device_id
            $user = User::where('user_hash', $userIdentifier)
                ->orWhere('device_id', $userIdentifier)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Get latest completed session
            $session = $user->mealSessions()
                ->where('status', 'completed')
                ->latest()
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'No completed meal plan found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'session_id' => $session->id,
                    'goal' => $session->goal,
                    'goal_explanation' => $session->goal_explanation,
                    'total_days' => $session->total_days,
                    'meal_plan' => $session->meal_data,
                    'daily_totals' => $session->daily_totals,
                    'summary' => [
                        'total_calories' => $session->total_calories,
                        'total_protein' => $session->total_protein,
                        'total_carbs' => $session->total_carbs,
                        'total_fat' => $session->total_fat,
                        'total_price' => $session->total_price,
                        'total_meals' => $session->total_meals
                    ],
                    'generated_at' => $session->completed_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get Meal Plan Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve meal plan',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get session status
     */
    public function getSessionStatus($sessionId)
    {
        try {
            $session = DB::table('meal_sessions')->find($sessionId);

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'session_id' => $session->id,
                    'status' => $session->status,
                    'current_day' => $session->current_day,
                    'total_days' => $session->total_days,
                    'progress' => $session->total_days > 0 
                        ? round(($session->current_day / $session->total_days) * 100, 2) 
                        : 0,
                    'error_message' => $session->error_message,
                    'started_at' => $session->started_at,
                    'completed_at' => $session->completed_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get Session Status Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve session status',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Download meal plan as PDF
     */
    public function downloadPdf(Request $request, $userIdentifier)
    {
        try {
            // Find user
            $user = User::where('user_hash', $userIdentifier)
                ->orWhere('device_id', $userIdentifier)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Get latest completed session
            $session = $user->mealSessions()
                ->where('status', 'completed')
                ->latest()
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'No completed meal plan found'
                ], 404);
            }

            // Prepare data for PDF
            $data = [
                'meal_plan' => $session->meal_data,
                'user_info' => [
                    'age' => $user->age,
                    'height' => $user->height,
                    'weight' => $user->weight,
                    'gender' => $user->gender,
                    'activity_level' => $user->activity_level,
                    'bmi' => $user->bmi,
                    'bmi_overview' => $user->bmi_overview,
                    'bmr' => $user->bmr,
                    'tdee' => $user->tdee,
                    'body_fat' => $user->body_fat
                ],
                'current_day' => $session->total_days,
                'goal_decision' => $session->goal,
                'goal_explanation' => $session->goal_explanation,
                'generated_date' => $session->completed_at->format('F j, Y'),
                'total_days' => $session->total_days,
                'summary' => [
                    'total_cal' => $session->total_calories,
                    'total_protein' => $session->total_protein,
                    'total_carbs' => $session->total_carbs,
                    'total_fat' => $session->total_fat,
                    'total_price' => $session->total_price,
                    'total_meals' => $session->total_meals,
                    'avg_cal_per_day' => $session->total_days > 0 
                        ? round($session->total_calories / $session->total_days, 2) 
                        : 0,
                    'avg_protein_per_day' => $session->total_days > 0 
                        ? round($session->total_protein / $session->total_days, 2) 
                        : 0,
                    'avg_carbs_per_day' => $session->total_days > 0 
                        ? round($session->total_carbs / $session->total_days, 2) 
                        : 0,
                    'avg_fat_per_day' => $session->total_days > 0 
                        ? round($session->total_fat / $session->total_days, 2) 
                        : 0,
                    'avg_price_per_day' => $session->total_days > 0 
                        ? round($session->total_price / $session->total_days, 2) 
                        : 0,
                ]
            ];

            // Generate PDF
            $pdf = \PDF::loadView('pdf.meal-plan', $data);
            $pdf->setPaper('A4', 'portrait');

            $filename = 'meal-plan-' . $user->user_hash . '-' . now()->format('Y-m-d') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Download PDF Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
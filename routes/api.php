<?php

use App\Http\Controllers\WebController;
use App\Http\Controllers\Api\MobileApiController;
use App\Http\Controllers\Api\StreamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Existing endpoint
Route::post('/v1/generate-ai-meal', [WebController::class, 'generateUserAiMeal'])->name('generate-user-ai-meal');

// Mobile API Endpoints (No Authentication Required)
Route::prefix('v1/mobile')->group(function () {
    // Main endpoint to generate meals
    Route::post('/generate-meals', [MobileApiController::class, 'generateMeals'])->name('api.mobile.generate');
    
    // SSE streaming endpoint
    Route::get('/stream/{sessionId}', [StreamController::class, 'stream'])->name('api.mobile.stream');
    
    // Get session status
    Route::get('/session/{sessionId}/status', [MobileApiController::class, 'getSessionStatus'])->name('api.mobile.session.status');
    
    // Get existing meal plan
    Route::get('/meal-plan/{userIdentifier}', [MobileApiController::class, 'getMealPlan'])->name('api.mobile.meal-plan');
    
    // Download meal plan as PDF
    Route::get('/meal-plan/{userIdentifier}/pdf', [MobileApiController::class, 'downloadPdf'])->name('api.mobile.meal-plan.pdf');
});


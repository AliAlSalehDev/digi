<?php

use App\Http\Controllers\IngredientController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SauceController;
use App\Http\Controllers\WebController;
use App\Models\Ingredient;
use App\Models\Sauce;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/hassan/bhagnywala',[WebController::class,'downloadpdf'])->name('download.pdf');
Route::get('/', [WebController::class, 'index']);
Route::post('save/user-info', [WebController::class, 'saveUserInfo'])->name('user-info');
Route::get('/generate-meal', [WebController::class, 'generateMeal']);
Route::post('/generate-ai-meal', [WebController::class, 'generateAiMeal'])->name('generate-ai-meal');
Route::post('/reset-meal-plan', [WebController::class, 'resetMealPlan'])->name('reset-meal-plan');
Route::post('/continue-from-day', [WebController::class, 'continueFromDay'])->name('continue-from-day');
Route::get('/approve-meal', [WebController::class, 'approveMeal'])->name('approve-meal');use App\Http\Controllers\MealPlannerController;
use App\Models\Meal;

// Route::post('/save-user-info', [MealPlannerController::class, 'saveUserInfo'])->name('save-user-info');
// Route::get('/generate-meal', [MealPlannerController::class, 'generateMeal'])->name('generate-meal');
// Route::post('/approve-meal', [MealPlannerController::class, 'approveMeal'])->name('approve-meal');

Route::get('/dashboard', function () {
    $totalIngredients = Ingredient::count();
    $totalSauces = Sauce::count();
    $totalMeals = Meal::count();
    return view('dashboard', compact('totalIngredients','totalSauces','totalMeals'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Route::view('about', 'about')->name('about');

    // Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::resource('ingredients', IngredientController::class);
    Route::resource('sauces', SauceController::class);
    Route::resource('meals', MealController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoadmapController;
use App\Http\Controllers\Api\VoiceSessionController;
use App\Http\Controllers\Api\BusinessPlanController;
use App\Http\Controllers\Api\UserProgressController;
use App\Http\Controllers\Api\AdvisorController;

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

// Roadmap routes (require authentication)
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/roadmap', [RoadmapController::class, 'show']);
    Route::post('/roadmap/update', [RoadmapController::class, 'update']);
});

// Voice session routes (require authentication)
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/voice-session', [VoiceSessionController::class, 'store']);
    Route::patch('/voice-session/{id}', [VoiceSessionController::class, 'update']);
});

// Business plan routes (require authentication)
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/business-plan', [BusinessPlanController::class, 'show']);
    Route::post('/business-plan/update', [BusinessPlanController::class, 'update']);
});

// User progress routes (require authentication)
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/user-progress', [UserProgressController::class, 'show']);
    Route::post('/user-progress/award-xp', [UserProgressController::class, 'awardXP']);
});

// Advisor routes (require authentication)
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/advisors', [AdvisorController::class, 'index']);
    Route::post('/advisors/match', [AdvisorController::class, 'matchByStep']);
});


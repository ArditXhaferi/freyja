<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoadmapController;
use App\Http\Controllers\Api\VoiceSessionController;

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


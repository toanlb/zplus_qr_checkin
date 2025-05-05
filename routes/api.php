<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MobileCheckInController;
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

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Check-in functionality
    Route::post('/checkin/scan', [MobileCheckInController::class, 'processQrCode']);
    Route::get('/checkin/history', [MobileCheckInController::class, 'getCheckInHistory']);
});
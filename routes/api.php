<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MufapApiController;

// Public Routes
Route::post('/register', [AuthController::class, 'register']); // User signup
Route::post('/login', [AuthController::class, 'login']); // User login

// Protected Routes (need Sanctum token)
Route::middleware('auth:sanctum')->group(function () {

      // User
      Route::get('/user', [AuthController::class, 'profile']); // Get logged-in user profile
      Route::post('/logout', [AuthController::class, 'logout']); // Logout user

      // MUFAP Data
      Route::get('/mutualfunds', [MufapApiController::class, 'index']); // All funds
      Route::get('/mutualfunds/{id}', [MufapApiController::class, 'show']); // Single fund
      Route::post('/mutualfunds', [MufapApiController::class, 'store']); // Add new fund
      Route::put('/mutualfunds/{id}', [MufapApiController::class, 'update']); // Update fund
      Route::delete('/mutualfunds/{id}', [MufapApiController::class, 'destroy']); // Delete fund

      // Extra APIs for Filtering / Searching
      Route::get('/mutualfunds/sector/{sector}', [MufapApiController::class, 'filterBySector']);
      Route::get('/mutualfunds/amc/{amc}', [MufapApiController::class, 'filterByAMC']);
      Route::get('/mutualfunds/category/{category}', [MufapApiController::class, 'filterByCategory']);
      Route::get('/mutualfunds/date/{date}', [MufapApiController::class, 'filterByDate']);
});

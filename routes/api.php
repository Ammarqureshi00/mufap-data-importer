<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MufapApiController;

// Public Routes

Route::post('login', [AuthController::class, 'login']); // User login
Route::post('/logout', [AuthController::class, 'logout']); // Logout user

// MUFAP Data
Route::get('/mutualfunds', [MufapApiController::class, 'index']); // All funds
Route::get('/mutualfunds/{id}', [MufapApiController::class, 'show']); // Single fundDelete fund

// Extra APIs for Filtering / Searching

Route::get('/mutualfunds/sectors', [MufapApiController::class, 'getAllSectors']);
Route::get('/mutualfunds/amcs', [MufapApiController::class, 'getAllAMCs']);
Route::get('/mutualfunds/categories', [MufapApiController::class, 'getAllCategories']);


// Route::get('/mutualfunds/date', [MufapApiController::class, 'filterByDate']);
// Route::get('/mutualfunds/date/{date}', [MufapApiController::class, 'filterByDate']);
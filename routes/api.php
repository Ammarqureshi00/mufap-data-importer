<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MufapApiController;
use App\Http\Controllers\Api\MufapDateRangeController;

// Public Routes

Route::post('login', [AuthController::class, 'login']); // User login
Route::post('/logout', [AuthController::class, 'logout']); // Logout user

// MUFAP Data
Route::get('/mutualfunds', [MufapApiController::class, 'index']); // All funds

// Extra APIs for Filtering / Searching
Route::get('mutualfunds/categories', [MufapApiController::class, 'getAllCategories']);
Route::get('mutualfunds/sectors', [MufapApiController::class, 'getAllSectors']);
Route::get('mutualfunds/amcs', [MufapApiController::class, 'getAllAMCs']);
Route::get('mutualfunds/{id}', [MufapApiController::class, 'show']); // <-- this goes LAST
Route::get('/mutualfunds/{id}/history', [MufapDateRangeController::class, 'getDateRange']); // Fund history by ID
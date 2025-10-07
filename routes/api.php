<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MufapApiController;

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
Route::get('/mutualfunds/{id}/history', [MufapApiController::class, 'searchFunds']); // Fund history by ID
// Route::get('/mutualfunds/date', [MufapApiController::class, 'filterByDate']);
// Route::get('/mutualfunds/date/{date}', [MufapApiController::class, 'filterByDate']);
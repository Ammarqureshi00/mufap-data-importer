<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MfApiController;
use App\Http\Controllers\Api\MfhistoryController;
use App\Http\Controllers\MfScrapingController;

// Public Routes

Route::post('login', [AuthController::class, 'login']); // User login
Route::post('/logout', [AuthController::class, 'logout']); // Logout user

// MUFAP Data
Route::get('/mutualfunds', [MfApiController::class, 'index']); // All funds
Route::get('/mf-scrape-data/{date}', [MfScrapingController::class, 'scrapeDaily']);


// Extra APIs for Filtering / Searching
Route::get('mutualfunds/categories', [MfApiController::class, 'getAllCategories']);
Route::get('mutualfunds/sectors', [MfApiController::class, 'getAllSectors']);
Route::get('mutualfunds/amcs', [MfApiController::class, 'getAllAMCs']);
Route::get('mutualfunds/{id}', [MfApiController::class, 'show']); // <-- this goes LAST
Route::get('/mutualfunds/{id}/history', [MfhistoryController::class, 'gethistory']); // Fund history by ID
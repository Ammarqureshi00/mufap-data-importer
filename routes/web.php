<?php

use App\Http\Controllers\MfCsvDataController;
use Illuminate\Support\Facades\Route;
// web  routes for Csv upload and view

Route::get('/mufap-data', [MfCsvDataController::class, 'index'])->name('mufap.index');
Route::post('/mufap-data/upload', [MfCsvDataController::class, 'uploadCsv'])->name('mufap.upload');

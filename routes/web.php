<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MufapDataController;

Route::get('/mufap-data', [MufapDataController::class, 'index'])->name('mufap.index');
Route::post('/mufap-data/upload', [MufapDataController::class, 'uploadCsv'])->name('mufap.upload');

<?php

use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('dashboard.access')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('api.dashboard');
});

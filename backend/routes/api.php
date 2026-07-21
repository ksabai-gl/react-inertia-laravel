<?php

use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', DashboardController::class)
    ->middleware('dashboard.api.access')
    ->name('api.dashboard');

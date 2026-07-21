<?php

use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:' . config('app.dashboard_api_rate_limit', '60,1'))
    ->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('api.dashboard');
    });

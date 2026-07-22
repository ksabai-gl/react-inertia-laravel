<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('dashboard.access')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::redirect('/dashboard', '/');
});

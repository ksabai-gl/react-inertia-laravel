<?php

use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Account\SecurityController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::redirect('/dashboard', '/');

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/account/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/account/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/account/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/account/security', [SecurityController::class, 'show'])->name('security.show');
});

require __DIR__.'/auth.php';
require __DIR__.'/fortify.php';

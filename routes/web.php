<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MenuController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', function () {
    return view('Login');
});

Route::post('/admin-login', [AuthenticatedSessionController::class, 'login'])
->name('login');

Route::get('/login-link/{user}', [AuthenticatedSessionController::class, 'handleLoginLink'])
    ->name('handle.login.link')
    ->middleware('signed');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/menu-search', [MenuController::class, 'search'])->name('menu.search');


require __DIR__.'/auth.php';

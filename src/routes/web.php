<?php

use App\Http\Controllers\Admin\UserPromotionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');

Route::get('/cadastro', [RegisterController::class, 'create'])->name('register');
Route::post('/cadastro', [RegisterController::class, 'store'])->name('register.store');

Route::get('/dashboard', DashboardController::class)
    ->middleware('auth')
    ->name('dashboard');

Route::view('/usuario', 'usuario.dashboard')
    ->middleware('auth')
    ->name('usuario.dashboard');

Route::view('/admin', 'admin.dashboard')
    ->middleware(['auth', 'admin'])
    ->name('admin.dashboard');

Route::post('/usuarios/{user}/promover-admin', UserPromotionController::class)
    ->middleware(['auth', 'admin'])
    ->name('users.promote-admin');

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
    ]);
});

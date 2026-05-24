<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');

Route::get('/cadastro', [RegisterController::class, 'create'])->name('register');
Route::post('/cadastro', [RegisterController::class, 'store'])->name('register.store');

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
    ]);
});

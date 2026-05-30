<?php

use App\Http\Controllers\Admin\LocalColetaController;
use App\Http\Controllers\Admin\UserPromotionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ContaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Doador\CarteiraDoacaoController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');

Route::get('/cadastro', [RegisterController::class, 'create'])->name('register');
Route::post('/cadastro', [RegisterController::class, 'store'])->name('register.store');

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/conta', [ContaController::class, 'edit'])->name('conta.edit');
    Route::put('/conta', [ContaController::class, 'update'])->name('conta.update');
    Route::delete('/conta', [ContaController::class, 'destroy'])->name('conta.destroy');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::view('/usuario', 'usuario.dashboard')->name('usuario.dashboard');
    Route::get('/usuario/carteirinha', [CarteiraDoacaoController::class, 'create'])
        ->name('usuario.carteirinha');
    Route::post('/usuario/carteirinha', [CarteiraDoacaoController::class, 'store'])
        ->name('usuario.carteirinha.store');
    Route::put('/usuario/carteirinha', [CarteiraDoacaoController::class, 'update'])
        ->name('usuario.carteirinha.update');

    Route::middleware('admin')->group(function (): void {
        Route::view('/admin', 'admin.dashboard')->name('admin.dashboard');
        Route::get('/admin/locais-coleta', [LocalColetaController::class, 'index'])
            ->name('admin.locais-coleta.index');
        Route::post('/admin/locais-coleta', [LocalColetaController::class, 'store'])
            ->name('admin.locais-coleta.store');
        Route::put('/admin/locais-coleta/{localColeta}', [LocalColetaController::class, 'update'])
            ->name('admin.locais-coleta.update');
        Route::delete('/admin/locais-coleta/{localColeta}', [LocalColetaController::class, 'destroy'])
            ->name('admin.locais-coleta.destroy');
        Route::post('/usuarios/{user}/promover-admin', UserPromotionController::class)
            ->name('users.promote-admin');
    });
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
    ]);
});

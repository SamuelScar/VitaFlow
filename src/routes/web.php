<?php

use App\Http\Controllers\Admin\CampanhaController;
use App\Http\Controllers\Admin\LocalColetaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserPromotionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ContaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Doador\CarteiraDoacaoController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::get('/cadastro', [RegisterController::class, 'create'])->name('register');
Route::post('/cadastro', [RegisterController::class, 'store'])->name('register.store');
Route::get('/esqueci-senha', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');
Route::post('/esqueci-senha', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');
Route::get('/redefinir-senha/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');
Route::post('/redefinir-senha', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.update');

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/conta', [ContaController::class, 'edit'])->name('conta.edit');
    Route::put('/conta', [ContaController::class, 'update'])->name('conta.update');
    Route::delete('/conta', [ContaController::class, 'destroy'])->name('conta.destroy');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::middleware('doador')->group(function (): void {
        Route::view('/usuario', 'usuario.dashboard')->name('usuario.dashboard');
        Route::get('/usuario/carteirinha', [CarteiraDoacaoController::class, 'create'])
            ->name('usuario.carteirinha');
        Route::post('/usuario/carteirinha', [CarteiraDoacaoController::class, 'store'])
            ->name('usuario.carteirinha.store');
        Route::put('/usuario/carteirinha', [CarteiraDoacaoController::class, 'update'])
            ->name('usuario.carteirinha.update');
    });

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
        Route::get('/admin/campanhas', [CampanhaController::class, 'index'])
            ->name('admin.campanhas.index');
        Route::post('/admin/campanhas', [CampanhaController::class, 'store'])
            ->name('admin.campanhas.store');
        Route::put('/admin/campanhas/{campanha}', [CampanhaController::class, 'update'])
            ->name('admin.campanhas.update');
        Route::delete('/admin/campanhas/{campanha}', [CampanhaController::class, 'destroy'])
            ->name('admin.campanhas.destroy');
        Route::get('/admin/usuarios', [UserController::class, 'index'])
            ->name('admin.usuarios.index');
        Route::post('/admin/usuarios/{user}/promover-admin', UserPromotionController::class)
            ->name('users.promote-admin');
    });
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
    ]);
});

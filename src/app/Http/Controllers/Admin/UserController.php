<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConviteAdmin;
use Illuminate\View\View;

/**
 * Exibe a listagem administrativa de usuários (renderizada via componente Livewire).
 */
class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.usuarios.index', [
            'convitesPendentes' => ConviteAdmin::with('convidadoPor')
                ->whereNull('aceito_em')
                ->whereNull('cancelado_em')
                ->latest()
                ->get(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Gerencia o cadastro de novos usuários. Todo usuário criado por este fluxo entra com perfil `doador`.
 */
class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create($data);

        return redirect()
            ->route('register')
            ->with('success', 'Cadastro realizado com sucesso. Voce sera redirecionado para o login.')
            ->with('alert_redirect', route('login'))
            ->with('alert_timer', 3000);
    }
}

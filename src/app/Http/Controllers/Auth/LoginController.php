<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Gerencia o fluxo de autenticação por sessão: exibição do formulário, tentativa de login e logout.
 */
class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Tenta autenticar com as credenciais enviadas. Em caso de falha, lança ValidationException para exibir o erro no campo de e-mail. Em caso de sucesso, regenera a sessão e redireciona para o destino pretendido.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'Credenciais invalidas.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}

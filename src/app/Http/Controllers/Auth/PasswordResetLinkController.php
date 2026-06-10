<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Gerencia o envio do link de redefinição de senha por e-mail.
 */
class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Envia o link de redefinição para o e-mail informado. Retorna a mesma resposta independentemente de o e-mail existir no banco, evitando enumeração de usuários.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            $status = Password::sendResetLink($data);
        } catch (TransportExceptionInterface $exception) {
            Log::error('Falha ao enviar e-mail de redefinicao de senha.', [
                'exception' => $exception,
            ]);

            return back()
                ->withErrors(['email' => 'Nao foi possivel enviar o e-mail de redefinicao. Tente novamente mais tarde.'])
                ->onlyInput('email');
        }

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', __($status))
            : back()->withErrors(['email' => __($status)])->onlyInput('email');
    }
}

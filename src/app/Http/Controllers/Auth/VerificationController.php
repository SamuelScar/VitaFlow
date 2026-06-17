<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * Marca o e-mail do usuário como verificado, mesmo que não esteja logado.
     */
    public function verify(Request $request, string $id, string $hash): \Illuminate\View\View
    {
        $user = \App\Models\User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403);
        }

        if (! $request->hasValidSignature()) {
            abort(403);
        }

        if ($user->hasVerifiedEmail()) {
            return view('auth.verified');
        }

        $user->markEmailAsVerified();
        event(new \Illuminate\Auth\Events\Verified($user));

        return view('auth.verified');
    }

    /**
     * Reenvia o e-mail de verificação para o usuário.
     */
    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard')->with('success', 'Seu e-mail já foi verificado.');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'O link de verificação foi enviado para seu e-mail.');
    }
}

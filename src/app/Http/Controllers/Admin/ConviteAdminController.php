<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ConviteAdminMail;
use App\Models\ConviteAdmin;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

/**
 * Gerencia convites para criação de contas administrativas.
 */
class ConviteAdminController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $admin = $request->user();
        assert($admin !== null);
        $this->normalizarEmail($request);

        $data = $request->validateWithBag('convidarAdmin', [
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
        ]);

        $convite = ConviteAdmin::where('email', $data['email'])->first();

        if ($convite?->aceito_em !== null) {
            return back()->withErrors([
                'email' => 'Este convite ja foi aceito.',
            ], 'convidarAdmin')->onlyInput('email');
        }

        if ($convite !== null && $convite->cancelado_em === null && ! $convite->estaExpirado()) {
            return back()->withErrors([
                'email' => 'Ja existe um convite pendente para este e-mail.',
            ], 'convidarAdmin')->onlyInput('email');
        }

        $convite ??= new ConviteAdmin(['email' => $data['email']]);
        $token = $convite->renovar($admin);

        return $this->enviar($convite, $token)
            ? back()->with('success', 'Convite administrativo enviado com sucesso.')
            : back()->withErrors([
                'email' => 'O convite foi criado, mas nao foi possivel enviar o e-mail. Tente reenviar.',
            ], 'convidarAdmin');
    }

    public function resend(Request $request, ConviteAdmin $conviteAdmin): RedirectResponse
    {
        $admin = $request->user();
        assert($admin !== null);

        if ($conviteAdmin->aceito_em !== null || $conviteAdmin->cancelado_em !== null) {
            return back()->withErrors([
                'convite' => 'Somente convites pendentes ou expirados podem ser reenviados.',
            ]);
        }

        if (User::where('email', $conviteAdmin->email)->exists()) {
            return back()->withErrors([
                'convite' => 'Este e-mail ja pertence a um usuario cadastrado.',
            ]);
        }

        $token = $conviteAdmin->renovar($admin);

        return $this->enviar($conviteAdmin, $token)
            ? back()->with('success', 'Convite administrativo reenviado com sucesso.')
            : back()->withErrors([
                'convite' => 'Nao foi possivel reenviar o convite.',
            ]);
    }

    public function destroy(ConviteAdmin $conviteAdmin): RedirectResponse
    {
        if ($conviteAdmin->aceito_em !== null || $conviteAdmin->cancelado_em !== null) {
            return back()->withErrors([
                'convite' => 'Este convite nao pode mais ser cancelado.',
            ]);
        }

        $conviteAdmin->cancelar();

        return back()->with('success', 'Convite administrativo cancelado.');
    }

    private function normalizarEmail(Request $request): void
    {
        $request->merge([
            'email' => Str::lower(trim((string) $request->input('email'))),
        ]);
    }

    private function enviar(ConviteAdmin $convite, string $token): bool
    {
        try {
            Mail::to($convite->email)->send(new ConviteAdminMail(
                $convite,
                route('convites-admin.accept', $token),
            ));

            return true;
        } catch (Throwable $exception) {
            Log::error('Falha ao enviar convite administrativo.', [
                'convite_admin_id' => $convite->id,
                'exception' => $exception,
            ]);

            return false;
        }
    }
}

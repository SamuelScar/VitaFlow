<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ConviteAdmin;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Gerencia o aceite de convites para criação de contas administrativas.
 */
class AceiteConviteAdminController extends Controller
{
    public function create(string $token): View
    {
        $convite = $this->buscarConvite($token);
        abort_unless($convite?->podeSerAceito(), 410, 'Este convite nao esta mais disponivel.');
        abort_if(User::where('email', $convite->email)->exists(), 410, 'Este e-mail ja esta cadastrado.');

        return view('auth.aceitar-convite-admin', [
            'convite' => $convite,
            'token' => $token,
        ]);
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        DB::transaction(function () use ($data, $token): void {
            $convite = ConviteAdmin::where('token_hash', hash('sha256', $token))
                ->lockForUpdate()
                ->first();

            if (! $convite?->podeSerAceito()) {
                throw ValidationException::withMessages([
                    'convite' => 'Este convite nao esta mais disponivel.',
                ]);
            }

            if (User::where('email', $convite->email)->exists()) {
                throw ValidationException::withMessages([
                    'convite' => 'Este e-mail ja pertence a um usuario cadastrado.',
                ]);
            }

            User::forceCreate([
                'name' => $data['name'],
                'email' => $convite->email,
                'tipo' => User::TIPO_ADMIN,
                'email_verified_at' => now(),
                'password' => $data['password'],
            ]);

            $convite->aceitar();
        });

        return redirect()->route('login')->with('success', 'Conta administrativa criada com sucesso.');
    }

    private function buscarConvite(string $token): ?ConviteAdmin
    {
        return ConviteAdmin::where('token_hash', hash('sha256', $token))->first();
    }
}

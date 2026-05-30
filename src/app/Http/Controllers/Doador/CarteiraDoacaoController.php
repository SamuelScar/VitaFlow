<?php

namespace App\Http\Controllers\Doador;

use App\Http\Controllers\Controller;
use App\Support\TiposSanguineos;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CarteiraDoacaoController extends Controller
{
    public function create(Request $request): View
    {
        if (! $request->user()?->isDoador()) {
            abort(403);
        }

        return view('usuario.carteirinha');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $cpf = preg_replace('/\D/', '', (string) $request->input('cpf', ''));

        $request->merge([
            'cpf' => $cpf ?? '',
        ]);

        if ($user === null || ! $user->isDoador()) {
            abort(403);
        }

        if ($user->carteiraDoacao()->exists()) {
            return back()->withErrors([
                'carteira' => 'Sua carteirinha de doador ja foi emitida.',
            ]);
        }

        $data = $request->validate([
            'cpf' => ['required', 'digits:11', 'unique:carteiras_doacao,cpf'],
            'telefone' => ['required', 'string', 'max:20'],
            'data_nascimento' => ['required', 'date', 'before_or_equal:today'],
            'tipo_sanguineo' => ['required', Rule::in(TiposSanguineos::TODOS)],
            'peso' => ['required', 'numeric', 'min:0.01', 'max:999.99'],
            'cidade' => ['required', 'string', 'max:255'],
        ]);

        $user->carteiraDoacao()->create([
            ...$data,
            'status' => 'ativa',
            'emitida_em' => now()->toDateString(),
        ]);

        return back()->with('success', 'Carteirinha de doador emitida com sucesso.');
    }
}

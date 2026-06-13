<?php

namespace App\Http\Controllers\Doador;

use App\Http\Controllers\Controller;
use App\Support\TipoSanguineo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Gerencia a emissão da carteirinha e os dados do usuário exibidos nela.
 * Cada usuário doador pode ter no máximo uma carteirinha.
 */
class CarteiraDoacaoController extends Controller
{
    public function create(Request $request): View
    {
        return view('usuario.carteirinha');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        assert($user !== null);
        $this->normalizeCpf($request);

        if ($user->carteiraDoacao()->exists()) {
            return back()->withErrors([
                'carteira' => 'Sua carteirinha de doador ja foi emitida.',
            ]);
        }

        $data = $request->validate($this->rules($user->id));

        DB::transaction(function () use ($data, $user): void {
            $user->update($data);
            $user->carteiraDoacao()->create([
                'status' => 'ativa',
                'emitida_em' => now()->toDateString(),
            ]);
        });

        return back()->with('success', 'Carteirinha de doador emitida com sucesso.');
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        assert($user !== null);
        $this->normalizeCpf($request);

        $carteira = $user->carteiraDoacao;

        if ($carteira === null) {
            return back()->withErrors([
                'carteira' => 'Emita sua carteirinha antes de editar os dados.',
            ]);
        }

        $user->update($request->validate($this->rules($user->id)));

        return back()->with('success', 'Dados do usuario atualizados com sucesso.');
    }

    /**
     * Remove todos os caracteres não numéricos do CPF no request antes da validação, aceitando tanto CPF mascarado quanto somente dígitos.
     */
    private function normalizeCpf(Request $request): void
    {
        $cpf = preg_replace('/\D/', '', (string) $request->input('cpf', ''));

        $request->merge([
            'cpf' => $cpf ?? '',
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(int $userId): array
    {
        return [
            'cpf'             => ['required', 'digits:11', Rule::unique('users', 'cpf')->ignore($userId)],
            'telefone'        => ['required', 'string', 'max:20'],
            'data_nascimento' => ['required', 'date', 'before_or_equal:today'],
            'tipo_sanguineo'  => ['required', Rule::in(TipoSanguineo::values())],
            'peso'            => ['required', 'numeric', 'min:0.01', 'max:999.99'],
            'cidade'          => ['required', 'string', 'max:255'],
        ];
    }
}

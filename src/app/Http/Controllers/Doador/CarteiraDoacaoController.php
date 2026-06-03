<?php

namespace App\Http\Controllers\Doador;

use App\Http\Controllers\Controller;
use App\Support\TipoSanguineo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Gerencia a emissão e edição da carteirinha de doador. Cada usuário doador pode ter no máximo uma carteirinha.
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

        $data = $request->validate($this->rules());

        $user->carteiraDoacao()->create([
            ...$data,
            'status'     => 'ativa',
            'emitida_em' => now()->toDateString(),
        ]);

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

        $carteira->update($request->validate($this->rules($carteira->id)));

        return back()->with('success', 'Dados da carteirinha atualizados com sucesso.');
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
    private function rules(?int $carteiraId = null): array
    {
        $cpfRule = Rule::unique('carteiras_doacao', 'cpf');

        if ($carteiraId !== null) {
            $cpfRule->ignore($carteiraId);
        }

        return [
            'cpf'             => ['required', 'digits:11', $cpfRule],
            'telefone'        => ['required', 'string', 'max:20'],
            'data_nascimento' => ['required', 'date', 'before_or_equal:today'],
            'tipo_sanguineo'  => ['required', Rule::in(TipoSanguineo::values())],
            'peso'            => ['required', 'numeric', 'min:0.01', 'max:999.99'],
            'cidade'          => ['required', 'string', 'max:255'],
        ];
    }
}

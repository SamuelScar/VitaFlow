<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campanha;
use App\Models\LocalColeta;
use App\Support\TipoSanguineo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Gerencia o CRUD de campanhas de doação de sangue.
 */
class CampanhaController extends Controller
{
    public function index(): View
    {
        $campanhas = Campanha::with(['localColeta', 'criador'])
            ->orderByDesc('data_inicio')
            ->orderBy('titulo')
            ->paginate(20);

        return view('admin.campanhas.index', [
            'campanhas'       => $campanhas,
            'totalCampanhas'  => $campanhas->total(),
            'locaisColeta'    => LocalColeta::orderBy('nome')->get(),
            'tiposSanguineos' => TipoSanguineo::values(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        assert($user !== null);

        $user->campanhasCriadas()->create([
            ...$this->validatedData($request, 'storeCampanha'),
            'status' => 'ativa',
        ]);

        return back()->with('success', 'Campanha cadastrada com sucesso.');
    }

    public function update(Request $request, Campanha $campanha): RedirectResponse
    {
        $campanha->update($this->validatedData($request, 'updateCampanha', true));

        return back()->with('success', 'Campanha atualizada com sucesso.');
    }

    public function destroy(Campanha $campanha): RedirectResponse
    {
        if ($campanha->agendamentos()->exists()) {
            return back()->withErrors([
                'campanha' => 'Nao e possivel excluir uma campanha com agendamentos vinculados.',
            ]);
        }

        $campanha->delete();

        return back()->with('success', 'Campanha excluida com sucesso.');
    }

    /**
     * Valida os dados da requisição e garante que `tipos_sanguineos_alvo` seja null quando não enviado (em vez de array vazio), respeitando a semântica do campo.
     *
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, string $errorBag, bool $updating = false): array
    {
        $data = $request->validateWithBag($errorBag, $this->rules($updating));
        $data['tipos_sanguineos_alvo'] = $data['tipos_sanguineos_alvo'] ?? null;

        return $data;
    }

    /**
     * Retorna as regras de validação. No cadastro, exige que `data_inicio` seja hoje ou futura. Na edição, inclui a regra para o campo `status`.
     *
     * @return array<string, array<int, mixed>>
     */
    private function rules(bool $updating = false): array
    {
        $rules = [
            'local_coleta_id' => ['required', 'integer', Rule::exists('locais_coleta', 'id')],
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['required', 'string', 'max:5000'],
            'tipos_sanguineos_alvo' => ['nullable', 'array'],
            'tipos_sanguineos_alvo.*' => ['required', 'distinct', Rule::in(TipoSanguineo::values())],
            'meta_bolsas' => ['required', 'integer', 'min:1', 'max:100000'],
            'agendamentos_por_horario' => ['required', 'integer', 'min:1', 'max:100'],
            'horario_inicio' => ['required', 'date_format:H:i'],
            'horario_fim' => ['required', 'date_format:H:i', 'after:horario_inicio'],
            'data_inicio' => ['required', 'date'],
            'data_fim' => ['required', 'date', 'after_or_equal:data_inicio'],
        ];

        if (! $updating) {
            $rules['data_inicio'][] = 'after_or_equal:today';
        }

        if ($updating) {
            $rules['status'] = ['required', Rule::in(['ativa', 'encerrada', 'cancelada'])];
        }

        return $rules;
    }
}

<?php

namespace App\Livewire\Admin;

use App\Models\Agendamento;
use App\Models\Campanha;
use App\Models\LocalColeta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AgendamentoList extends Component
{
    use WithPagination;

    private const ITENS_POR_PAGINA = [10, 20, 50, 100];
    private const STATUS = [
        Agendamento::STATUS_AGENDADO,
        Agendamento::STATUS_CANCELADO,
        Agendamento::STATUS_REALIZADO,
        Agendamento::STATUS_FALTOU,
    ];
    private const STATUS_DOACAO = ['confirmada', 'recusada'];

    protected string $paginationTheme = 'bootstrap';

    #[Url(as: 'campanha', except: '')]
    public string $campanhaId = '';

    #[Url(as: 'local', except: '')]
    public string $localColetaId = '';

    #[Url(as: 'status', except: '')]
    public string $status = '';

    #[Url(as: 'de', except: '')]
    public string $dataInicio = '';

    #[Url(as: 'ate', except: '')]
    public string $dataFim = '';

    #[Url(as: 'por_pagina', except: 20)]
    public int $porPagina = 20;

    public bool $campanhaTravada = false;

    public ?int $doacaoAgendamentoId = null;

    public string $doacaoStatus = 'confirmada';

    public string $quantidadeMl = '450';

    public string $motivoRecusa = '';

    public function mount(string $campanhaId = '', bool $campanhaTravada = false): void
    {
        $this->campanhaTravada = $campanhaTravada;

        if ($campanhaId !== '') {
            $this->campanhaId = $campanhaId;
        }
    }

    public function updatedCampanhaId(): void
    {
        $this->resetPage();
    }

    public function updatedLocalColetaId(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedDataInicio(): void
    {
        $this->resetPage();
    }

    public function updatedDataFim(): void
    {
        $this->resetPage();
    }

    public function updatedPorPagina(): void
    {
        if (! in_array($this->porPagina, self::ITENS_POR_PAGINA, true)) {
            $this->porPagina = 20;
        }

        $this->resetPage();
    }

    public function limparFiltros(): void
    {
        if (! $this->campanhaTravada) {
            $this->campanhaId = '';
        }

        $this->localColetaId = '';
        $this->status = '';
        $this->dataInicio = '';
        $this->dataFim = '';
        $this->resetPage();
    }

    public function updatedDoacaoStatus(): void
    {
        $this->resetValidation(['quantidadeMl', 'motivoRecusa']);
    }

    public function marcarComparecimento(int $agendamentoId): void
    {
        $this->registrarComparecimento(
            $agendamentoId,
            Agendamento::STATUS_REALIZADO,
            'Comparecimento registrado com sucesso.',
        );
    }

    public function marcarFalta(int $agendamentoId): void
    {
        $this->registrarComparecimento(
            $agendamentoId,
            Agendamento::STATUS_FALTOU,
            'Falta registrada com sucesso.',
        );
    }

    public function cancelarOperacionalmente(int $agendamentoId): void
    {
        $this->registrarComparecimento(
            $agendamentoId,
            Agendamento::STATUS_CANCELADO,
            'Cancelamento operacional registrado com sucesso.',
        );
    }

    public function iniciarRegistroDoacao(int $agendamentoId): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $agendamento = Agendamento::query()
            ->with('doacao')
            ->findOrFail($agendamentoId);

        if (! $agendamento->podeRegistrarDoacao()) {
            $this->dispatch('alert-error', message: 'Este agendamento nao permite registro de doacao.');

            return;
        }

        $this->doacaoAgendamentoId = $agendamentoId;
        $this->doacaoStatus = 'confirmada';
        $this->quantidadeMl = '450';
        $this->motivoRecusa = '';
        $this->resetValidation();
    }

    public function cancelarRegistroDoacao(): void
    {
        $this->limparRegistroDoacao();
    }

    public function registrarDoacao(): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        if ($this->doacaoAgendamentoId === null) {
            $this->dispatch('alert-error', message: 'Selecione um agendamento para registrar a doacao.');

            return;
        }

        $this->validate($this->regrasRegistroDoacao(), $this->mensagensRegistroDoacao());

        $resultado = DB::transaction(function (): string {
            $agendamento = Agendamento::query()
                ->with(['campanha', 'doacao', 'user'])
                ->lockForUpdate()
                ->findOrFail($this->doacaoAgendamentoId);

            if (! $agendamento->podeRegistrarDoacao()) {
                return 'invalido';
            }

            if (
                $this->doacaoStatus === 'confirmada'
                && ($agendamento->campanha === null || $agendamento->user?->tipo_sanguineo === null)
            ) {
                return 'dados_incompletos';
            }

            $agendamento->doacao()->create([
                'data_coleta' => now(),
                'quantidade_ml' => $this->doacaoStatus === 'confirmada' ? (int) $this->quantidadeMl : null,
                'status' => $this->doacaoStatus,
                'motivo_recusa' => $this->doacaoStatus === 'recusada' ? trim($this->motivoRecusa) : null,
            ]);

            return 'registrada';
        });

        if ($resultado === 'invalido') {
            $this->dispatch('alert-error', message: 'Este agendamento ja possui doacao ou nao esta marcado como realizado.');

            return;
        }

        if ($resultado === 'dados_incompletos') {
            $this->dispatch(
                'alert-error',
                message: 'Nao foi possivel gerar a bolsa: verifique campanha e tipo sanguineo do doador.',
            );

            return;
        }

        $this->limparRegistroDoacao();
        $this->dispatch('alert-success', message: 'Doacao registrada com sucesso.');
    }

    public function render(): View
    {
        $query = $this->agendamentosQuery();

        $resumoStatus = (clone $query)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('livewire.admin.agendamento-list', [
            'agendamentos' => $query
                ->latest('data_hora')
                ->paginate($this->porPagina),
            'campanhaTravada' => $this->campanhaTravada,
            'campanhas' => $this->campanhaTravada
                ? collect()
                : Campanha::orderBy('titulo')->get(['id', 'titulo']),
            'filtrosAtivos' => $this->temFiltrosAtivos(),
            'locaisColeta' => $this->campanhaTravada
                ? collect()
                : LocalColeta::orderBy('nome')->get(['id', 'nome']),
            'opcoesPorPagina' => self::ITENS_POR_PAGINA,
            'resumoStatus' => $resumoStatus,
            'statusDoacaoOptions' => self::STATUS_DOACAO,
            'statusOptions' => self::STATUS,
        ]);
    }

    private function agendamentosQuery(): Builder
    {
        $campanhaId = ctype_digit($this->campanhaId) ? (int) $this->campanhaId : null;
        $localColetaId = ctype_digit($this->localColetaId) ? (int) $this->localColetaId : null;
        $status = in_array($this->status, self::STATUS, true) ? $this->status : null;

        return Agendamento::query()
            ->with(['user', 'campanha.localColeta', 'doacao'])
            ->when($campanhaId !== null, fn (Builder $query) => $query->where('campanha_id', $campanhaId))
            ->when($localColetaId !== null, function (Builder $query) use ($localColetaId): void {
                $query->whereHas('campanha', fn (Builder $query) => $query->where('local_coleta_id', $localColetaId));
            })
            ->when($status !== null, fn (Builder $query) => $query->where('status', $status))
            ->when($this->dataInicio !== '', fn (Builder $query) => $query->whereDate('data_hora', '>=', $this->dataInicio))
            ->when($this->dataFim !== '', fn (Builder $query) => $query->whereDate('data_hora', '<=', $this->dataFim));
    }

    private function temFiltrosAtivos(): bool
    {
        return (! $this->campanhaTravada && $this->campanhaId !== '')
            || $this->localColetaId !== ''
            || $this->status !== ''
            || $this->dataInicio !== ''
            || $this->dataFim !== '';
    }

    private function registrarComparecimento(int $agendamentoId, string $status, string $mensagemSucesso): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $registrado = DB::transaction(function () use ($agendamentoId, $status): bool {
            $agendamento = Agendamento::query()
                ->with('doacao')
                ->lockForUpdate()
                ->findOrFail($agendamentoId);

            if (! $agendamento->podeRegistrarComparecimento() || $agendamento->status === $status) {
                return false;
            }

            $agendamento->update(['status' => $status]);

            return true;
        });

        if (! $registrado) {
            $this->dispatch(
                'alert-error',
                message: 'Este agendamento nao esta dentro da janela de registro, ja possui doacao ou nao precisa desta alteracao.',
            );

            return;
        }

        $this->dispatch('alert-success', message: $mensagemSucesso);
    }

    private function limparRegistroDoacao(): void
    {
        $this->doacaoAgendamentoId = null;
        $this->doacaoStatus = 'confirmada';
        $this->quantidadeMl = '450';
        $this->motivoRecusa = '';
        $this->resetValidation();
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function regrasRegistroDoacao(): array
    {
        $rules = [
            'doacaoStatus' => ['required', Rule::in(self::STATUS_DOACAO)],
        ];

        if ($this->doacaoStatus === 'confirmada') {
            $rules['quantidadeMl'] = ['required', 'integer', 'min:1', 'max:1000'];
        }

        if ($this->doacaoStatus === 'recusada') {
            $rules['motivoRecusa'] = ['required', 'string', 'max:5000'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    private function mensagensRegistroDoacao(): array
    {
        return [
            'doacaoStatus.required' => 'Selecione o resultado da doacao.',
            'doacaoStatus.in' => 'Selecione um resultado valido.',
            'quantidadeMl.required' => 'Informe a quantidade coletada.',
            'quantidadeMl.integer' => 'Informe a quantidade em mililitros.',
            'quantidadeMl.min' => 'A quantidade deve ser maior que zero.',
            'quantidadeMl.max' => 'A quantidade deve ter no maximo 1000 ml.',
            'motivoRecusa.required' => 'Informe o motivo da recusa.',
            'motivoRecusa.max' => 'O motivo da recusa deve ter no maximo 5000 caracteres.',
        ];
    }
}

<?php

namespace App\Livewire\Admin;

use App\Models\Agendamento;
use App\Models\Campanha;
use App\Models\LocalColeta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AgendamentoList extends Component
{
    use WithPagination;

    private const ITENS_POR_PAGINA = [10, 20, 50, 100];
    private const STATUS = ['agendado', 'cancelado', 'realizado', 'faltou'];

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
        $this->campanhaId = '';
        $this->localColetaId = '';
        $this->status = '';
        $this->dataInicio = '';
        $this->dataFim = '';
        $this->resetPage();
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
            'campanhas' => Campanha::orderBy('titulo')->get(['id', 'titulo']),
            'filtrosAtivos' => $this->temFiltrosAtivos(),
            'locaisColeta' => LocalColeta::orderBy('nome')->get(['id', 'nome']),
            'opcoesPorPagina' => self::ITENS_POR_PAGINA,
            'resumoStatus' => $resumoStatus,
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
        return $this->campanhaId !== ''
            || $this->localColetaId !== ''
            || $this->status !== ''
            || $this->dataInicio !== ''
            || $this->dataFim !== '';
    }
}

<?php

namespace App\Livewire\Admin;

use App\Jobs\ArquivarRelatorioPdf;
use App\Jobs\DesarquivarRelatorioPdf;
use App\Models\RelatorioExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class MeusRelatorios extends Component
{
    use WithPagination;

    public array $selecionados = [];
    public bool $selecionarTodos = false;

    public function updatedSelecionarTodos($value): void
    {
        if ($value) {
            $this->selecionados = $this->getRelatoriosQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selecionados = [];
        }
    }

    public function updatedSelecionados(): void
    {
        $this->selecionarTodos = false;
    }

    public function arquivarEmMassa(): void
    {
        if (empty($this->selecionados)) {
            $this->dispatch('alert-error', message: 'Nenhum relatório selecionado.');
            return;
        }

        $exports = RelatorioExport::where('user_id', auth()->id())
            ->whereIn('id', $this->selecionados)
            ->where('is_arquivado', false)
            ->get();

        foreach ($exports as $export) {
            if ($export->concluido()) {
                $export->forceFill(['status' => RelatorioExport::STATUS_ARQUIVANDO])->save();
                ArquivarRelatorioPdf::dispatch($export->id);
            }
        }

        $this->selecionados = [];
        $this->selecionarTodos = false;
        $this->dispatch('alert-success', message: 'Relatórios enviados para arquivamento.');
    }

    public function desarquivarEmMassa(): void
    {
        if (empty($this->selecionados)) {
            $this->dispatch('alert-error', message: 'Nenhum relatório selecionado.');
            return;
        }

        $exports = RelatorioExport::where('user_id', auth()->id())
            ->whereIn('id', $this->selecionados)
            ->where('is_arquivado', true)
            ->get();

        foreach ($exports as $export) {
            $export->forceFill(['status' => RelatorioExport::STATUS_DESARQUIVANDO])->save();
            DesarquivarRelatorioPdf::dispatch($export->id);
        }

        $this->selecionados = [];
        $this->selecionarTodos = false;
        $this->dispatch('alert-success', message: 'Relatórios enviados para desarquivamento.');
    }

    public function excluirEmMassa(): void
    {
        if (empty($this->selecionados)) {
            $this->dispatch('alert-error', message: 'Nenhum relatório selecionado.');
            return;
        }

        $exports = RelatorioExport::withTrashed()
            ->where('user_id', auth()->id())
            ->whereIn('id', $this->selecionados)
            ->get();

        foreach ($exports as $export) {
            if ($export->arquivo_path && Storage::disk('local')->exists($export->arquivo_path)) {
                Storage::disk('local')->delete($export->arquivo_path);
            }
            if (!$export->trashed()) {
                $export->delete();
            }
        }

        $this->selecionados = [];
        $this->selecionarTodos = false;
        $this->dispatch('alert-success', message: 'Relatórios excluídos com sucesso.');
    }

    private function getRelatoriosQuery()
    {
        return RelatorioExport::withTrashed()
            ->where('user_id', auth()->id())
            ->where('tipo', RelatorioExport::TIPO_PDF)
            ->latest();
    }

    #[Computed]
    public function podeArquivar(): bool
    {
        if (empty($this->selecionados)) {
            return false;
        }

        $invalidos = RelatorioExport::withTrashed()
            ->whereIn('id', $this->selecionados)
            ->where(function ($query) {
                $query->where('is_arquivado', true)
                      ->orWhere('status', '!=', RelatorioExport::STATUS_CONCLUIDO)
                      ->orWhereNotNull('deleted_at');
            })
            ->count();

        return $invalidos === 0;
    }

    #[Computed]
    public function podeDesarquivar(): bool
    {
        if (empty($this->selecionados)) {
            return false;
        }

        $invalidos = RelatorioExport::withTrashed()
            ->whereIn('id', $this->selecionados)
            ->where(function ($query) {
                $query->where('is_arquivado', false)
                      ->orWhere('status', '!=', RelatorioExport::STATUS_CONCLUIDO)
                      ->orWhereNotNull('deleted_at');
            })
            ->count();

        return $invalidos === 0;
    }

    #[Computed]
    public function podeExcluir(): bool
    {
        if (empty($this->selecionados)) {
            return false;
        }

        $invalidos = RelatorioExport::withTrashed()
            ->whereIn('id', $this->selecionados)
            ->whereIn('status', [
                RelatorioExport::STATUS_PROCESSANDO, 
                RelatorioExport::STATUS_ARQUIVANDO, 
                RelatorioExport::STATUS_DESARQUIVANDO
            ])
            ->count();

        return $invalidos === 0;
    }

    public function render(): View
    {
        return view('livewire.admin.meus-relatorios', [
            'relatorios' => $this->getRelatoriosQuery()->paginate(15),
        ])->layout('components.layouts.public', ['title' => 'Meus Relatórios']);
    }
}

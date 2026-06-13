<?php

namespace App\Livewire\Admin;

use App\Models\BolsaSangue;
use App\Models\EstoqueSangue;
use App\Models\LocalColeta;
use App\Support\TipoSanguineo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Lista bolsas, calcula o estoque e executa movimentações administrativas.
 */
class BolsaSangueList extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url(as: 'local', except: '')]
    public string $localId = '';

    #[Url(as: 'tipo', except: '')]
    public string $tipoSanguineo = '';

    #[Url(as: 'status', except: '')]
    public string $status = '';

    public function updatedLocalId(): void
    {
        $this->resetPage();
    }

    public function updatedTipoSanguineo(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function limparFiltros(): void
    {
        $this->localId = '';
        $this->tipoSanguineo = '';
        $this->status = '';
        $this->resetPage();
    }

    public function utilizar(int $bolsaId): void
    {
        $movimentada = $this->movimentar($bolsaId, function (BolsaSangue $bolsa): bool {
            $bolsa->utilizar();

            return true;
        });

        if (! $movimentada) {
            $this->alertarMovimentacaoInvalida();

            return;
        }

        $this->dispatch('alert-success', message: 'Utilizacao da bolsa registrada com sucesso.');
    }

    public function descartar(int $bolsaId): void
    {
        $movimentada = $this->movimentar($bolsaId, function (BolsaSangue $bolsa): bool {
            $bolsa->descartar();

            return true;
        });

        if (! $movimentada) {
            $this->alertarMovimentacaoInvalida();

            return;
        }

        $this->dispatch('alert-success', message: 'Descarte da bolsa registrado com sucesso.');
    }

    public function transferir(int $bolsaId, int $localDestinoId): void
    {
        $destino = LocalColeta::find($localDestinoId);

        if ($destino === null) {
            $this->dispatch('alert-error', message: 'Selecione um local de destino valido.');

            return;
        }

        $movimentada = $this->movimentar($bolsaId, function (BolsaSangue $bolsa) use ($destino): bool {
            if ($destino->id === $bolsa->local_coleta_id) {
                return false;
            }

            $bolsa->transferir($destino);

            return true;
        });

        if (! $movimentada) {
            $this->alertarMovimentacaoInvalida();

            return;
        }

        $this->dispatch('alert-success', message: 'Transferencia da bolsa registrada com sucesso.');
    }

    public function atualizarEstoqueMinimo(int $estoqueId, mixed $quantidadeMl): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $quantidadeMl = filter_var($quantidadeMl, FILTER_VALIDATE_INT);

        if ($quantidadeMl === false || $quantidadeMl < 0 || $quantidadeMl > 1000000) {
            $this->dispatch('alert-error', message: 'O estoque minimo deve ficar entre 0 e 1.000.000 ml.');

            return;
        }

        EstoqueSangue::findOrFail($estoqueId)->update([
            'estoque_minimo_ml' => $quantidadeMl,
        ]);

        $this->dispatch('alert-success', message: 'Estoque minimo atualizado com sucesso.');
    }

    public function render(): View
    {
        $localId = ctype_digit($this->localId) ? (int) $this->localId : null;
        $tipoSanguineo = in_array($this->tipoSanguineo, TipoSanguineo::values(), true)
            ? $this->tipoSanguineo
            : null;
        $status = in_array($this->status, $this->statusValidos(), true) ? $this->status : null;
        $locais = LocalColeta::orderBy('nome')->get();

        $estoqueCalculado = BolsaSangue::disponiveis()
            ->selectRaw('local_coleta_id, tipo_sanguineo, count(*) as total_bolsas, sum(quantidade_ml) as total_ml')
            ->groupBy('local_coleta_id', 'tipo_sanguineo')
            ->get()
            ->keyBy(fn (BolsaSangue $bolsa) => "{$bolsa->local_coleta_id}|{$bolsa->tipo_sanguineo}");

        $estoques = EstoqueSangue::with('localColeta')
            ->when($localId !== null, fn (Builder $query) => $query->where('local_coleta_id', $localId))
            ->when($tipoSanguineo !== null, fn (Builder $query) => $query->where('tipo_sanguineo', $tipoSanguineo))
            ->orderBy('local_coleta_id')
            ->orderBy('tipo_sanguineo')
            ->paginate(15, ['*'], 'estoquePage')
            ->through(function (EstoqueSangue $configuracao) use ($estoqueCalculado): array {
                $calculado = $estoqueCalculado->get("{$configuracao->local_coleta_id}|{$configuracao->tipo_sanguineo}");
                $quantidadeMl = (int) ($calculado?->total_ml ?? 0);

                return [
                    'id' => $configuracao->id,
                    'local' => $configuracao->localColeta,
                    'tipo_sanguineo' => $configuracao->tipo_sanguineo,
                    'total_bolsas' => (int) ($calculado?->total_bolsas ?? 0),
                    'quantidade_ml' => $quantidadeMl,
                    'estoque_minimo_ml' => $configuracao->estoque_minimo_ml,
                    'abaixo_minimo' => $quantidadeMl < $configuracao->estoque_minimo_ml,
                ];
            });

        return view('livewire.admin.bolsa-sangue-list', [
            'bolsas' => BolsaSangue::query()
                ->with(['localColeta', 'doacao.agendamento.user'])
                ->when($localId !== null, fn (Builder $query) => $query->where('local_coleta_id', $localId))
                ->when($tipoSanguineo !== null, fn (Builder $query) => $query->where('tipo_sanguineo', $tipoSanguineo))
                ->when($status !== null, fn (Builder $query) => $this->filtrarStatus($query, $status))
                ->orderByDesc('data_coleta')
                ->paginate(15, ['*'], 'bolsaPage'),
            'estoques' => $estoques,
            'totalBolsasDisponiveis' => $estoqueCalculado->sum('total_bolsas'),
            'locais' => $locais,
            'tiposSanguineos' => TipoSanguineo::values(),
        ]);
    }

    private function movimentar(int $bolsaId, callable $movimentacao): bool
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        return DB::transaction(function () use ($bolsaId, $movimentacao): bool {
            $bolsa = BolsaSangue::lockForUpdate()->findOrFail($bolsaId);

            if (! $bolsa->estaDisponivel()) {
                return false;
            }

            return $movimentacao($bolsa);
        });
    }

    private function alertarMovimentacaoInvalida(): void
    {
        $this->dispatch(
            'alert-error',
            message: 'A bolsa nao esta disponivel ou a movimentacao informada nao e valida.',
        );
    }

    private function filtrarStatus(Builder $query, string $status): Builder
    {
        return match ($status) {
            BolsaSangue::STATUS_DISPONIVEL => $query
                ->whereIn('status', [BolsaSangue::STATUS_DISPONIVEL, BolsaSangue::STATUS_TRANSFERIDA])
                ->where('validade_em', '>', now()),
            BolsaSangue::STATUS_VENCIDA => $query
                ->where(function (Builder $query): void {
                    $query
                        ->where('status', BolsaSangue::STATUS_VENCIDA)
                        ->orWhere(function (Builder $query): void {
                            $query
                                ->whereIn('status', [BolsaSangue::STATUS_DISPONIVEL, BolsaSangue::STATUS_TRANSFERIDA])
                                ->where('validade_em', '<=', now());
                        });
                }),
            BolsaSangue::STATUS_TRANSFERIDA => $query
                ->where('status', BolsaSangue::STATUS_TRANSFERIDA)
                ->where('validade_em', '>', now()),
            default => $query->where('status', $status),
        };
    }

    /**
     * @return string[]
     */
    private function statusValidos(): array
    {
        return [
            BolsaSangue::STATUS_DISPONIVEL,
            BolsaSangue::STATUS_UTILIZADA,
            BolsaSangue::STATUS_VENCIDA,
            BolsaSangue::STATUS_DESCARTADA,
            BolsaSangue::STATUS_TRANSFERIDA,
        ];
    }
}

<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Lista usuários com busca, filtro por perfil e totais agrupados.
 */
class UserList extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url(as: 'busca', except: '')]
    public string $busca = '';

    #[Url(as: 'tipo', except: '')]
    public string $tipo = '';

    public function updatedBusca(): void
    {
        $this->resetPage();
    }

    public function updatedTipo(): void
    {
        $this->resetPage();
    }

    public function limparFiltros(): void
    {
        $this->busca = '';
        $this->tipo = '';
        $this->resetPage();
    }

    public function alterarStatusCarteirinha(int $userId): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $user = User::with('carteiraDoacao')->findOrFail($userId);
        $carteira = $user->carteiraDoacao;

        if (! $user->isDoador() || $carteira === null) {
            $this->dispatch(
                'alert-error',
                message: 'Somente doadores com carteirinha emitida podem ter seu status alterado.',
            );

            return;
        }

        $novoStatus = $carteira->status === 'ativa' ? 'inativa' : 'ativa';
        $carteira->update(['status' => $novoStatus]);

        $this->dispatch('alert-success', message: "Carteirinha {$novoStatus} com sucesso.");
    }

    public function render(): View
    {
        $busca = trim($this->busca);
        $tipo = in_array($this->tipo, [User::TIPO_ADMIN, User::TIPO_DOADOR], true)
            ? $this->tipo
            : '';

        $contagens = User::selectRaw('tipo, count(*) as total')
            ->groupBy('tipo')
            ->pluck('total', 'tipo')
            ->map(fn ($v) => (int) $v);

        return view('livewire.admin.user-list', [
            'usuarios' => User::query()
                ->with('carteiraDoacao')
                ->when($busca !== '', function (Builder $query) use ($busca): void {
                    $query->where(function (Builder $query) use ($busca): void {
                        $query->where('name', 'ilike', "%{$busca}%")
                            ->orWhere('email', 'ilike', "%{$busca}%");
                    });
                })
                ->when($tipo !== '', fn (Builder $query) => $query->where('tipo', $tipo))
                ->orderBy('name')
                ->orderBy('email')
                ->paginate(10),
            'totalUsuarios' => $contagens->sum(),
            'totalAdmins'   => $contagens->get(User::TIPO_ADMIN, 0),
            'totalDoadores' => $contagens->get(User::TIPO_DOADOR, 0),
        ]);
    }
}

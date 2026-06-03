<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Componente Livewire para listagem paginada de usuários com busca em tempo real. A busca filtra por nome ou e-mail (case-insensitive via ILIKE). Os totais por perfil são calculados em uma única query agrupada.
 */
class UserList extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url(as: 'busca', except: '')]
    public string $busca = '';

    public function updatedBusca(): void
    {
        $this->resetPage();
    }

    public function limparBusca(): void
    {
        $this->busca = '';
        $this->resetPage();
    }

    public function render(): View
    {
        $busca = trim($this->busca);

        $contagens = User::selectRaw('tipo, count(*) as total')
            ->groupBy('tipo')
            ->pluck('total', 'tipo')
            ->map(fn ($v) => (int) $v);

        return view('livewire.admin.user-list', [
            'usuarios' => User::query()
                ->when($busca !== '', function (Builder $query) use ($busca): void {
                    $query->where(function (Builder $query) use ($busca): void {
                        $query->where('name', 'ilike', "%{$busca}%")
                            ->orWhere('email', 'ilike', "%{$busca}%");
                    });
                })
                ->orderBy('name')
                ->orderBy('email')
                ->paginate(10),
            'totalUsuarios' => $contagens->sum(),
            'totalAdmins'   => $contagens->get(User::TIPO_ADMIN, 0),
            'totalDoadores' => $contagens->get(User::TIPO_DOADOR, 0),
        ]);
    }
}

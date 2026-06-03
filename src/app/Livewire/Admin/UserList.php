<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

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
            'totalUsuarios' => User::count(),
            'totalAdmins' => User::where('tipo', User::TIPO_ADMIN)->count(),
            'totalDoadores' => User::where('tipo', User::TIPO_DOADOR)->count(),
        ]);
    }
}

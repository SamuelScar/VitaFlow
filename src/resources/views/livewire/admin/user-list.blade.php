<div>
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
        <div>
            <h2 class="h5 fw-bold mb-1">Usuarios cadastrados</h2>
            <p class="text-secondary mb-0">A promocao altera apenas o perfil de acesso do usuario.</p>
        </div>

        <div class="d-flex flex-wrap align-items-start gap-2">
            <span class="badge text-bg-light border">
                <i class="bi bi-people me-1" aria-hidden="true"></i>
                {{ $totalUsuarios }} {{ $totalUsuarios === 1 ? 'usuario' : 'usuarios' }}
            </span>
            <span class="badge text-bg-light border">
                <i class="bi bi-shield-check me-1" aria-hidden="true"></i>
                {{ $totalAdmins }} {{ $totalAdmins === 1 ? 'admin' : 'admins' }}
            </span>
            <span class="badge text-bg-light border">
                <i class="bi bi-person-heart me-1" aria-hidden="true"></i>
                {{ $totalDoadores }} {{ $totalDoadores === 1 ? 'doador' : 'doadores' }}
            </span>
            @if (trim($busca) !== '')
                <span class="badge text-bg-primary">
                    <i class="bi bi-search me-1" aria-hidden="true"></i>
                    {{ $usuarios->total() }} {{ $usuarios->total() === 1 ? 'resultado' : 'resultados' }}
                </span>
            @endif
        </div>
    </div>

    <div class="row g-2 mb-4">
        <div class="col-12 col-lg">
            <label class="form-label fw-semibold" for="busca">Buscar usuario</label>
            <input
                class="form-control"
                id="busca"
                type="search"
                wire:model.live.debounce.350ms="busca"
                placeholder="Nome ou e-mail"
                autocomplete="off"
            >
        </div>
        <div class="col-12 col-lg-auto d-flex align-items-end gap-2">
            <button class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" type="button" wire:click="limparBusca" @disabled(trim($busca) === '')>
                <i class="bi bi-x-lg" aria-hidden="true"></i>
                Limpar
            </button>
        </div>
    </div>

    <div wire:loading.class="opacity-50" wire:target="busca,limparBusca,previousPage,nextPage,gotoPage">
        @forelse ($usuarios as $usuario)
            <div class="border rounded-3 p-3 mb-3" wire:key="usuario-{{ $usuario->id }}">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                    <div class="flex-grow-1">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <h3 class="h6 fw-bold mb-0">{{ $usuario->name }}</h3>
                            @if ($usuario->isAdmin())
                                <span class="badge text-bg-primary">
                                    <i class="bi bi-shield-check me-1" aria-hidden="true"></i>
                                    Administrador
                                </span>
                            @else
                                <span class="badge text-bg-light border">
                                    <i class="bi bi-person-heart me-1" aria-hidden="true"></i>
                                    Doador
                                </span>
                            @endif
                        </div>

                        <p class="text-secondary mb-0">{{ $usuario->email }}</p>
                    </div>

                    <div class="d-grid d-sm-flex flex-sm-nowrap flex-shrink-0 align-items-start justify-content-sm-end gap-2">
                        @if ($usuario->isDoador())
                            <form
                                class="d-grid m-0"
                                method="POST"
                                action="{{ route('users.promote-admin', $usuario) }}"
                                onsubmit="return confirm('Promover este usuario para administrador?')"
                            >
                                @csrf
                                <button class="btn btn-outline-primary d-inline-flex align-items-center justify-content-center gap-2" type="submit">
                                    <i class="bi bi-shield-plus" aria-hidden="true"></i>
                                    Promover
                                </button>
                            </form>
                        @else
                            <button class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-2" type="button" disabled>
                                <i class="bi bi-check-lg" aria-hidden="true"></i>
                                Ja e admin
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="border rounded-3 p-4 text-center">
                <h3 class="h6 fw-bold mb-1">
                    {{ trim($busca) === '' ? 'Nenhum usuario cadastrado' : 'Nenhum usuario encontrado' }}
                </h3>
                <p class="text-secondary mb-0">
                    {{ trim($busca) === '' ? 'Novos usuarios aparecerao aqui apos o cadastro.' : 'Revise o nome ou e-mail buscado e tente novamente.' }}
                </p>
            </div>
        @endforelse
    </div>

    @if ($usuarios->hasPages())
        <div class="mt-4">
            {{ $usuarios->links() }}
        </div>
    @endif
</div>

<div>
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
        <div>
            <h2 class="h5 fw-bold mb-1">Usuarios cadastrados</h2>
            <p class="text-secondary mb-0">Administradores e doadores possuem perfis exclusivos.</p>
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
            @if (trim($busca) !== '' || $tipo !== '')
                <span class="badge text-bg-primary">
                    <i class="bi bi-search me-1" aria-hidden="true"></i>
                    {{ $usuarios->total() }} {{ $usuarios->total() === 1 ? 'resultado' : 'resultados' }}
                </span>
            @endif
        </div>
    </div>

    <div class="row g-2 mb-4">
        <div class="col-12 col-lg-8">
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
        <div class="col-12 col-lg-4">
            <label class="form-label fw-semibold" for="tipo">Perfil</label>
            <select class="form-select" id="tipo" wire:model.live="tipo">
                <option value="">Todos os perfis</option>
                <option value="admin">Administradores</option>
                <option value="doador">Doadores</option>
            </select>
        </div>
        <div class="col-12 d-flex justify-content-end gap-2">
            <button class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" type="button" wire:click="limparFiltros" @disabled(trim($busca) === '' && $tipo === '')>
                <i class="bi bi-x-lg" aria-hidden="true"></i>
                Limpar filtros
            </button>
        </div>
    </div>

    <div wire:loading.class="opacity-50" wire:target="busca,tipo,limparFiltros,previousPage,nextPage,gotoPage">
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

                        <p class="text-secondary mb-2">{{ $usuario->email }}</p>

                        @if ($usuario->isDoador())
                            @if ($usuario->carteiraDoacao)
                                <span class="badge {{ $usuario->carteiraDoacao->status === 'ativa' ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    Carteirinha {{ $usuario->carteiraDoacao->status }}
                                </span>
                            @else
                                <span class="badge text-bg-light border">Carteirinha nao emitida</span>
                            @endif
                        @endif
                    </div>

                    @if ($usuario->isDoador() && $usuario->carteiraDoacao)
                        <button
                            class="btn {{ $usuario->carteiraDoacao->status === 'ativa' ? 'btn-outline-danger' : 'btn-outline-success' }} flex-shrink-0 align-self-lg-center"
                            type="button"
                            wire:loading.attr="disabled"
                            wire:target="alterarStatusCarteirinha({{ $usuario->id }})"
                            x-on:click="
                                window.confirmAction({
                                    title: '{{ $usuario->carteiraDoacao->status === 'ativa' ? 'Inativar' : 'Ativar' }} carteirinha?',
                                    text: 'Esta alteracao afeta a permissao do doador para realizar agendamentos.',
                                    confirmButtonText: '{{ $usuario->carteiraDoacao->status === 'ativa' ? 'Inativar' : 'Ativar' }}',
                                    buttonColor: '{{ $usuario->carteiraDoacao->status === 'ativa' ? '#c62828' : '#198754' }}',
                                }).then((confirmed) => confirmed && $wire.alterarStatusCarteirinha({{ $usuario->id }}))
                            "
                        >
                            {{ $usuario->carteiraDoacao->status === 'ativa' ? 'Inativar carteirinha' : 'Ativar carteirinha' }}
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="border rounded-3 p-4 text-center">
                <h3 class="h6 fw-bold mb-1">
                    {{ trim($busca) === '' && $tipo === '' ? 'Nenhum usuario cadastrado' : 'Nenhum usuario encontrado' }}
                </h3>
                <p class="text-secondary mb-0">
                    {{ trim($busca) === '' && $tipo === '' ? 'Novos usuarios aparecerao aqui apos o cadastro.' : 'Revise os filtros aplicados e tente novamente.' }}
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

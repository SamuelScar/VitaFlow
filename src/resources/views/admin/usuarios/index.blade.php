<x-layouts.public title="Usuarios">
    <x-page-header
        label="Administracao"
        title="Usuarios"
        description="Consulte usuarios e convide novos administradores."
        icon="bi-shield-check"
        :back-href="route('admin.dashboard')"
    />

    <section class="container py-5">
        <article class="card shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-lg-5">
                        <h2 class="h5 fw-bold mb-1">Convidar administrador</h2>
                        <p class="text-secondary mb-4">O convidado recebera um link para criar uma conta exclusivamente administrativa.</p>

                        <form method="POST" action="{{ route('admin.convites-admin.store') }}" data-validate-form>
                            @csrf

                            <label class="form-label fw-semibold" for="email_convite_admin">E-mail</label>
                            <input
                                class="form-control @error('email', 'convidarAdmin') is-invalid @enderror"
                                id="email_convite_admin"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                required
                            >
                            @error('email', 'convidarAdmin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <button class="btn btn-primary d-inline-flex align-items-center gap-2 mt-3" type="submit">
                                <i class="bi bi-envelope-plus" aria-hidden="true"></i>
                                Enviar convite
                            </button>
                        </form>
                    </div>

                    <div class="col-lg-7">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <h2 class="h5 fw-bold mb-1">Convites pendentes</h2>
                                <p class="text-secondary mb-0">Convites expirados podem ser reenviados.</p>
                            </div>
                            <span class="badge text-bg-light border">
                                {{ $convitesPendentes->count() }} {{ $convitesPendentes->count() === 1 ? 'convite' : 'convites' }}
                            </span>
                        </div>

                        @forelse ($convitesPendentes as $convite)
                            <div class="border rounded-3 p-3 mb-2">
                                <div class="d-flex flex-column flex-xl-row justify-content-between gap-3">
                                    <div>
                                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                            <strong>{{ $convite->email }}</strong>
                                            <span class="badge {{ $convite->estaExpirado() ? 'text-bg-danger' : 'text-bg-warning' }}">
                                                {{ $convite->estaExpirado() ? 'Expirado' : 'Pendente' }}
                                            </span>
                                        </div>
                                        <p class="small text-secondary mb-0">
                                            Enviado por {{ $convite->convidadoPor?->name ?? 'administrador removido' }}
                                            · expira em {{ $convite->expira_em->format('d/m/Y H:i') }}
                                        </p>
                                    </div>

                                    <div class="d-grid d-sm-flex flex-shrink-0 gap-2">
                                        <form method="POST" action="{{ route('admin.convites-admin.resend', $convite) }}">
                                            @csrf
                                            @method('PUT')
                                            <button class="btn btn-outline-primary w-100" type="submit">
                                                <i class="bi bi-arrow-repeat me-1" aria-hidden="true"></i>
                                                Reenviar
                                            </button>
                                        </form>
                                        <form
                                            method="POST"
                                            action="{{ route('admin.convites-admin.destroy', $convite) }}"
                                            data-confirm-title="Cancelar convite?"
                                            data-confirm-text="O link enviado deixara de funcionar."
                                            data-confirm-button-text="Cancelar convite"
                                            data-confirm-button-color="#c62828"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger w-100" type="submit">
                                                <i class="bi bi-x-lg me-1" aria-hidden="true"></i>
                                                Cancelar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="border rounded-3 p-4 text-center">
                                <h3 class="h6 fw-bold mb-1">Nenhum convite pendente</h3>
                                <p class="text-secondary mb-0">Novos convites aparecerao aqui ate serem aceitos ou cancelados.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </article>

        <article class="card shadow-sm rounded-3">
            <div class="card-body p-4">
                <livewire:admin.user-list />
            </div>
        </article>
    </section>
</x-layouts.public>

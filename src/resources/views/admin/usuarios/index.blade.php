<x-layouts.public title="Usuarios">
    <section class="bg-white border-bottom">
        <div class="container py-5">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <span class="badge text-bg-light border mb-3">
                        <i class="bi bi-shield-check me-1" aria-hidden="true"></i>
                        Administracao
                    </span>
                    <h1 class="h2 fw-bold mb-2">Usuarios</h1>
                    <p class="text-secondary mb-0">
                        Consulte os usuarios cadastrados e promova doadores para administradores.
                    </p>
                </div>

                <a class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    Voltar
                </a>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <article class="card shadow-sm rounded-3">
            <div class="card-body p-4">
                <livewire:admin.user-list />
            </div>
        </article>
    </section>
</x-layouts.public>

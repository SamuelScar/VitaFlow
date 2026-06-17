<x-layouts.public title="Relatórios Dinâmicos">
    <section class="bg-white border-bottom">
        <div class="container py-5">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <span class="badge text-bg-light border mb-3">
                        <i class="bi bi-file-earmark-bar-graph me-1" aria-hidden="true"></i>
                        Administracao
                    </span>
                    <h1 class="h2 fw-bold mb-2">Relatorios dinamicos</h1>
                    <p class="text-secondary mb-0">
                        Construa relatorios personalizados selecionando os modulos, colunas e filtros desejados.
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
        <livewire:admin.relatorio-builder />
    </section>
</x-layouts.public>

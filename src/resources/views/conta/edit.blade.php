<x-layouts.public title="Dados da conta">
    @php
        $usuario = auth()->user();
    @endphp

    <section class="bg-white border-bottom">
        <div class="container py-5">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <span class="badge text-bg-light border mb-3">
                        <i class="bi bi-person-gear me-1" aria-hidden="true"></i>
                        Conta
                    </span>
                    <h1 class="h2 fw-bold mb-2">Dados da conta</h1>
                    <p class="text-secondary mb-0">
                        Atualize suas informacoes de acesso ao VitaFlow.
                    </p>
                </div>

                <a class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    Voltar
                </a>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <article class="card shadow-sm rounded-3">
            @include('conta.partials.update-form', ['usuario' => $usuario])
            @include('conta.partials.delete-account-panel')
        </article>
    </section>
</x-layouts.public>

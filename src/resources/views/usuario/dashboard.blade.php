<x-layouts.public title="Area do doador">
    <section class="dashboard-header border-bottom">
        <div class="container py-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge text-bg-light border mb-3">Area do doador</span>
                    <h1 class="h2 fw-bold mb-2">Ola, {{ auth()->user()->name }}</h1>
                    <p class="text-secondary mb-0">
                        Acompanhe campanhas abertas e veja os proximos passos para agendar uma doacao de sangue.
                    </p>
                </div>

                <div class="col-lg-4">
                    <div class="dashboard-summary p-4">
                        <p class="text-secondary small fw-semibold text-uppercase mb-2">Seu perfil</p>
                        <strong class="fs-4 d-block">Doador</strong>
                        <span class="text-secondary">Conta pronta para participar de campanhas de sangue.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <div class="row g-4">
            <div class="col-md-6 col-xl-4">
                <article class="card dashboard-card h-100">
                    <div class="card-body p-4">
                        <h2 class="h5">Campanhas abertas</h2>
                        <p class="text-secondary mb-4">Veja campanhas disponiveis e escolha onde deseja doar sangue.</p>
                        <a class="btn btn-primary" href="{{ route('home') }}">Ver campanhas</a>
                    </div>
                </article>
            </div>

            <div class="col-md-6 col-xl-4">
                <article class="card dashboard-card h-100">
                    <div class="card-body p-4">
                        <h2 class="h5">Minhas doacoes de sangue</h2>
                        <p class="text-secondary mb-4">Historico de doacoes e agendamentos ficara disponivel quando o fluxo for criado.</p>
                        <button class="btn btn-outline-secondary" type="button" disabled>Em breve</button>
                    </div>
                </article>
            </div>

            <div class="col-md-6 col-xl-4">
                <article class="card dashboard-card h-100">
                    <div class="card-body p-4">
                        <h2 class="h5">Dados da conta</h2>
                        <p class="text-secondary mb-4">Mantenha seus dados atualizados para participar das campanhas de sangue.</p>
                        <button class="btn btn-outline-secondary" type="button" disabled>Em breve</button>
                    </div>
                </article>
            </div>
        </div>
    </section>
</x-layouts.public>

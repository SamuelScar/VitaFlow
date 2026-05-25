<x-layouts.public title="Painel admin">
    <section class="dashboard-header border-bottom">
        <div class="container py-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge text-bg-light border mb-3">Painel administrativo</span>
                    <h1 class="h2 fw-bold mb-2">Gestao do VitaFlow</h1>
                    <p class="text-secondary mb-0">
                        Gerencie campanhas de sangue, acompanhe agendamentos e promova usuarios quando necessario.
                    </p>
                </div>

                <div class="col-lg-4">
                    <div class="dashboard-summary p-4">
                        <p class="text-secondary small fw-semibold text-uppercase mb-2">Acesso atual</p>
                        <strong class="fs-4 d-block">Administrador</strong>
                        <span class="text-secondary">{{ auth()->user()->email }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <div class="row g-4">
            <div class="col-md-6 col-xl-3">
                <article class="card dashboard-card h-100">
                    <div class="card-body p-4">
                        <h2 class="h5">Campanhas de sangue</h2>
                        <p class="text-secondary mb-4">Criacao e edicao de campanhas de doacao ficarao aqui.</p>
                        <button class="btn btn-outline-secondary" type="button" disabled>Em breve</button>
                    </div>
                </article>
            </div>

            <div class="col-md-6 col-xl-3">
                <article class="card dashboard-card h-100">
                    <div class="card-body p-4">
                        <h2 class="h5">Agendamentos</h2>
                        <p class="text-secondary mb-4">Acompanhamento das doacoes de sangue agendadas pelo sistema.</p>
                        <button class="btn btn-outline-secondary" type="button" disabled>Em breve</button>
                    </div>
                </article>
            </div>

            <div class="col-md-6 col-xl-3">
                <article class="card dashboard-card h-100">
                    <div class="card-body p-4">
                        <h2 class="h5">Usuarios</h2>
                        <p class="text-secondary mb-4">Promocao de doadores para administradores.</p>
                        <button class="btn btn-outline-secondary" type="button" disabled>Em breve</button>
                    </div>
                </article>
            </div>

            <div class="col-md-6 col-xl-3">
                <article class="card dashboard-card h-100">
                    <div class="card-body p-4">
                        <h2 class="h5">Home publica</h2>
                        <p class="text-secondary mb-4">Visualize a pagina que visitantes acessam.</p>
                        <a class="btn btn-primary" href="{{ route('home') }}">Abrir home</a>
                    </div>
                </article>
            </div>
        </div>
    </section>
</x-layouts.public>

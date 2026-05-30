<x-layouts.public title="Painel admin">
    <section class="bg-white border-bottom">
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
                    <div class="bg-white border rounded-3 shadow-sm p-4">
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
                <x-dashboard-card
                    title="Locais de coleta"
                    description="Cadastre e atualize locais onde as doacoes de sangue acontecem."
                    :href="route('admin.locais-coleta.index')"
                    button-label="Gerenciar locais"
                />
            </div>

            <div class="col-md-6 col-xl-3">
                <x-dashboard-card
                    title="Campanhas de sangue"
                    description="Crie, atualize e encerre campanhas de doacao de sangue."
                    :href="route('admin.campanhas.index')"
                    button-label="Gerenciar campanhas"
                />
            </div>

            <div class="col-md-6 col-xl-3">
                <x-dashboard-card
                    title="Agendamentos"
                    description="Acompanhamento das doacoes de sangue agendadas pelo sistema."
                />
            </div>

            <div class="col-md-6 col-xl-3">
                <x-dashboard-card
                    title="Usuarios"
                    description="Promocao de doadores para administradores."
                />
            </div>

            <div class="col-md-6 col-xl-3">
                <x-dashboard-card
                    title="Home publica"
                    description="Visualize a pagina que visitantes acessam."
                    :href="route('home')"
                    button-label="Abrir home"
                />
            </div>
        </div>
    </section>
</x-layouts.public>

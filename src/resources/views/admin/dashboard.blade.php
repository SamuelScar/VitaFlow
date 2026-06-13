<x-layouts.public title="Painel admin">
    @php
        $email = auth()->user()->email;
        [$emailName, $emailDomain] = array_pad(explode('@', $email, 2), 2, '');
        $maskedEmail = mb_substr($emailName, 0, min(2, mb_strlen($emailName))) . '***';

        if ($emailDomain !== '') {
            $maskedEmail .= '@' . $emailDomain;
        }
    @endphp

    <section class="bg-white border-bottom">
        <div class="container py-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge text-bg-light border mb-3">
                        <i class="bi bi-grid-1x2 me-1" aria-hidden="true"></i>
                        Painel administrativo
                    </span>
                    <h1 class="h2 fw-bold mb-2">Gestao do VitaFlow</h1>
                    <p class="text-secondary mb-0">
                        Gerencie campanhas de sangue, acompanhe agendamentos e convide administradores quando necessario.
                    </p>
                </div>

                <div class="col-lg-4">
                    <div class="bg-white border rounded-3 shadow-sm p-4">
                        <p class="text-secondary small fw-semibold text-uppercase mb-2">
                            <i class="bi bi-shield-check me-1" aria-hidden="true"></i>
                            Acesso atual
                        </p>
                        <strong class="fs-4 d-block">Administrador</strong>
                        <span class="text-secondary">{{ $maskedEmail }}</span>
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
                    icon="bi-geo-alt"
                />
            </div>

            <div class="col-md-6 col-xl-3">
                <x-dashboard-card
                    title="Campanhas de sangue"
                    description="Crie, atualize e encerre campanhas de doacao de sangue."
                    :href="route('admin.campanhas.index')"
                    button-label="Gerenciar campanhas"
                    icon="bi-megaphone"
                />
            </div>

            <div class="col-md-6 col-xl-3">
                <x-dashboard-card
                    title="Bolsas e estoque"
                    description="Consulte o estoque calculado e gerencie o ciclo das bolsas."
                    :href="route('admin.bolsas-sangue.index')"
                    button-label="Gerenciar bolsas"
                    icon="bi-droplet-half"
                />
            </div>

            <div class="col-md-6 col-xl-3">
                <x-dashboard-card
                    title="Usuarios"
                    description="Usuarios cadastrados e convites administrativos."
                    :href="route('admin.usuarios.index')"
                    button-label="Gerenciar usuarios"
                    icon="bi-people"
                />
            </div>

            <div class="col-md-6 col-xl-3">
                <x-dashboard-card
                    title="Home publica"
                    description="Visualize a pagina que visitantes acessam."
                    :href="route('home')"
                    button-label="Abrir home"
                    icon="bi-house"
                />
            </div>
        </div>
    </section>
</x-layouts.public>

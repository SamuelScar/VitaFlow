<x-layouts.public title="Area do doador">
    @php
        $usuario = auth()->user();
        $carteira = $usuario->carteiraDoacao;
    @endphp

    <section class="bg-white border-bottom">
        <div class="container py-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge text-bg-light border mb-3">
                        <i class="bi bi-person-heart me-1" aria-hidden="true"></i>
                        Area do doador
                    </span>
                    <h1 class="h2 fw-bold mb-2">Ola, {{ $usuario->name }}</h1>
                    <p class="text-secondary mb-0">
                        Acompanhe campanhas abertas e veja os proximos passos para agendar uma doacao de sangue.
                    </p>
                </div>

                <div class="col-lg-4">
                    <div class="bg-white border rounded-3 shadow-sm p-4">
                        <p class="text-secondary small fw-semibold text-uppercase mb-2">
                            <i class="bi bi-droplet-half me-1" aria-hidden="true"></i>
                            Seu perfil
                        </p>
                        <strong class="fs-4 d-block">Doador</strong>
                        <span class="text-secondary">
                            {{ $carteira ? 'Carteirinha emitida para participar das campanhas.' : 'Emita sua carteirinha para participar das campanhas.' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <div class="row g-4">
            <div class="col-md-6 col-xl-3">
                <x-dashboard-card
                    title="Campanhas abertas"
                    description="Veja campanhas disponiveis e escolha onde deseja doar sangue."
                    :href="route('home')"
                    button-label="Ver campanhas"
                    icon="bi-megaphone"
                />
            </div>

            <div class="col-md-6 col-xl-3">
                <x-dashboard-card
                    title="Meus agendamentos"
                    description="Consulte, cancele ou reagende suas proximas doacoes."
                    :href="route('usuario.agendamentos.index')"
                    button-label="Gerenciar agendamentos"
                    icon="bi-calendar-heart"
                />
            </div>

            <div class="col-md-6 col-xl-3">
                <x-dashboard-card
                    title="Minhas doacoes"
                    description="Veja seu impacto, volume doado e resultado das coletas."
                    :href="route('usuario.doacoes.index')"
                    button-label="Acessar historico"
                    icon="bi-droplet-fill"
                />
            </div>

            <div class="col-md-6 col-xl-3">
                <x-dashboard-card
                    title="Carteirinha de doador"
                    :description="$carteira ? 'Consulte seus dados de doador de sangue.' : 'Informe seus dados para emitir sua carteirinha.'"
                    :href="route('usuario.carteirinha')"
                    :button-label="$carteira ? 'Ver carteirinha' : 'Emitir carteirinha'"
                    icon="bi-person-vcard"
                />
            </div>
        </div>
    </section>
</x-layouts.public>

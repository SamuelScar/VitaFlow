<x-layouts.public title="Campanhas">
    @php
        $campanhas = [
            [
                'titulo' => 'Cestas basicas para familias',
                'descricao' => 'Apoio alimentar para familias em situacao de vulnerabilidade.',
                'meta' => 'R$ 8.000',
                'arrecadado' => 'R$ 4.850',
                'progresso' => 61,
                'status' => 'Ativa',
            ],
            [
                'titulo' => 'Kits de higiene e cuidado',
                'descricao' => 'Itens essenciais para abrigos e pontos de acolhimento.',
                'meta' => 'R$ 5.000',
                'arrecadado' => 'R$ 2.100',
                'progresso' => 42,
                'status' => 'Ativa',
            ],
            [
                'titulo' => 'Agasalhos para o inverno',
                'descricao' => 'Campanha de arrecadacao para pessoas em situacao de rua.',
                'meta' => 'R$ 6.500',
                'arrecadado' => 'R$ 5.720',
                'progresso' => 88,
                'status' => 'Reta final',
            ],
        ];
    @endphp

    <section class="home-hero border-bottom">
        <div class="container py-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="badge text-bg-light border mb-3">Campanhas abertas</span>
                    <h1 class="display-5 fw-bold mb-3">Doe para campanhas que precisam de apoio agora</h1>
                    <p class="lead text-secondary mb-4">
                        Acompanhe campanhas ativas, veja o progresso de cada arrecadacao e entre para doar quando quiser participar.
                    </p>
                </div>

                <div class="col-lg-5">
                    <div class="home-impact-panel p-4">
                        <p class="text-secondary small fw-semibold text-uppercase mb-2">Resumo publico</p>
                        <div class="d-grid gap-3">
                            <div>
                                <strong class="fs-2 d-block">3</strong>
                                <span class="text-secondary">campanhas em destaque</span>
                            </div>
                            <div>
                                <strong class="fs-2 d-block">R$ 12.670</strong>
                                <span class="text-secondary">arrecadados nas campanhas exibidas</span>
                            </div>
                            <div>
                                <strong class="fs-2 d-block">64%</strong>
                                <span class="text-secondary">progresso medio das metas</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
            <div>
                <h2 class="h3 mb-1">Campanhas em destaque</h2>
                <p class="text-secondary mb-0">Informacoes publicas de campanhas abertas.</p>
            </div>
        </div>

        <div class="row g-4">
            @foreach ($campanhas as $campanha)
                <div class="col-md-6 col-xl-4">
                    <x-campaign-card :campanha="$campanha" />
                </div>
            @endforeach
        </div>
    </section>
</x-layouts.public>

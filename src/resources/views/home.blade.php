<x-layouts.public title="Campanhas">
    @php
        $campanhas = [
            [
                'titulo' => 'Mutirao de sangue O negativo',
                'descricao' => 'Campanha para reforcar o estoque de um tipo sanguineo essencial em emergencias.',
                'meta' => '80 bolsas',
                'resultado' => '49 bolsas',
                'progresso' => 61,
                'status' => 'Ativa',
            ],
            [
                'titulo' => 'Doacao de sangue no sabado',
                'descricao' => 'Atendimento especial para doadores que nao conseguem comparecer durante a semana.',
                'meta' => '50 vagas',
                'resultado' => '21 agendadas',
                'progresso' => 42,
                'status' => 'Ativa',
            ],
            [
                'titulo' => 'Reposicao do estoque pediatrico',
                'descricao' => 'Mobilizacao para manter o atendimento de criancas que dependem de transfusao.',
                'meta' => '65 bolsas',
                'resultado' => '57 bolsas',
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
                    <h1 class="display-5 fw-bold mb-3">Doe sangue para campanhas que precisam de apoio agora</h1>
                    <p class="lead text-secondary mb-4">
                        Acompanhe campanhas ativas, veja o progresso dos estoques e entre para agendar sua doacao quando quiser participar.
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
                                <strong class="fs-2 d-block">127</strong>
                                <span class="text-secondary">doacoes registradas nas campanhas exibidas</span>
                            </div>
                            <div>
                                <strong class="fs-2 d-block">64%</strong>
                                <span class="text-secondary">progresso medio dos estoques</span>
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
                <p class="text-secondary mb-0">Informacoes publicas de campanhas de doacao de sangue.</p>
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

<x-layouts.public title="Campanhas">
    @php
        $totalCampanhas = $campanhas->count();
        $totalMetaBolsas = $campanhas->sum('meta_bolsas');
        $totalLocais = $campanhas->pluck('local_coleta_id')->filter()->unique()->count();
    @endphp

    <section class="bg-white border-bottom">
        <div class="container py-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="badge text-bg-light border mb-3">Campanhas abertas</span>
                    <h1 class="display-5 fw-bold mb-3">Doe sangue para campanhas que precisam de apoio agora</h1>
                    <p class="lead text-secondary mb-4">
                        Acompanhe campanhas ativas, veja locais de coleta e entre para participar quando quiser doar.
                    </p>
                </div>

                <div class="col-lg-5">
                    <div class="bg-white border rounded-3 shadow-sm p-4">
                        <p class="text-secondary small fw-semibold text-uppercase mb-2">Resumo publico</p>
                        <div class="d-grid gap-3">
                            <div>
                                <strong class="fs-2 d-block">{{ $totalCampanhas }}</strong>
                                <span class="text-secondary">{{ $totalCampanhas === 1 ? 'campanha aberta' : 'campanhas abertas' }}</span>
                            </div>
                            <div>
                                <strong class="fs-2 d-block">{{ $totalMetaBolsas }}</strong>
                                <span class="text-secondary">{{ $totalMetaBolsas === 1 ? 'bolsa como meta' : 'bolsas como meta' }}</span>
                            </div>
                            <div>
                                <strong class="fs-2 d-block">{{ $totalLocais }}</strong>
                                <span class="text-secondary">{{ $totalLocais === 1 ? 'local participante' : 'locais participantes' }}</span>
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
            @forelse ($campanhas as $campanha)
                <div class="col-md-6 col-xl-4">
                    <x-campaign-card :campanha="$campanha" />
                </div>
            @empty
                <div class="col-12">
                    <div class="border rounded-3 p-4 text-center">
                        <h3 class="h5 fw-bold mb-2">Nenhuma campanha aberta no momento</h3>
                        <p class="text-secondary mb-0">Novas campanhas de doacao de sangue serao exibidas aqui quando forem cadastradas.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </section>
</x-layouts.public>

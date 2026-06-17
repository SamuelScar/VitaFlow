<x-layouts.public title="Minhas Doacoes">
    <section class="bg-white border-bottom">
        <div class="container py-5">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <span class="badge text-bg-light border mb-3">
                        <i class="bi bi-droplet-fill text-danger me-1" aria-hidden="true"></i>
                        Area do doador
                    </span>
                    <h1 class="h2 fw-bold mb-2">Minhas Doacoes</h1>
                    <p class="text-secondary mb-0">
                        Acompanhe seu historico de coletas, volume doado e o impacto da sua solidariedade.
                    </p>
                </div>

                <a class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-2" href="{{ route('usuario.dashboard') }}">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    Voltar
                </a>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card shadow-sm rounded-3 border-0 bg-danger text-white h-100">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-heart-pulse-fill mb-3 d-block" style="font-size: 2.5rem;"></i>
                        <h2 class="display-6 fw-bold mb-1">{{ $totalDoacoes * 4 }}</h2>
                        <p class="mb-0 text-white-50">
                            Vidas Salvas (Estimativa)
                            <i class="bi bi-question-circle ms-1" tabindex="0" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-placement="top" data-bs-html="true" data-bs-content="Cada bolsa de sangue doada pode ser dividida em hemácias, plaquetas, plasma e crioprecipitado, ajudando a salvar até 4 vidas.<br><br><small class='opacity-75'>Referência: Ministério da Saúde</small>" style="cursor: pointer;"></i>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card shadow-sm rounded-3 h-100">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-droplet-fill text-danger mb-3 d-block" style="font-size: 2.5rem;"></i>
                        <h2 class="display-6 fw-bold mb-1 text-danger">{{ number_format($totalMl, 0, ',', '.') }}<span class="fs-4 text-secondary">mL</span></h2>
                        <p class="text-secondary mb-0">Volume Total Doado</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm rounded-3 h-100">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-clipboard2-check-fill text-success mb-3 d-block" style="font-size: 2.5rem;"></i>
                        <h2 class="display-6 fw-bold mb-1 text-success">{{ $totalDoacoes }}</h2>
                        <p class="text-secondary mb-0">Coletas Bem-sucedidas</p>
                    </div>
                </div>
            </div>
        </div>

        <article class="card shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h2 class="h5 fw-bold mb-1">Historico de Coletas</h2>
                    <p class="text-secondary mb-0">O resultado da triagem e da coleta de sangue em seus agendamentos realizados.</p>
                </div>

                @forelse ($doacoes as $doacao)
                    <div class="border rounded-3 p-3 mb-3">
                        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                            <div>
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                    <h3 class="h6 fw-bold mb-0">{{ $doacao->agendamento?->campanha?->titulo ?? 'Campanha indisponivel' }}</h3>
                                    
                                    @if ($doacao->status === 'confirmada')
                                        <span class="badge text-bg-success"><i class="bi bi-check-circle me-1"></i> Confirmada</span>
                                    @else
                                        <span class="badge text-bg-warning"><i class="bi bi-exclamation-triangle me-1"></i> Recusada na Triagem</span>
                                    @endif
                                </div>
                                <div class="d-flex flex-wrap gap-2 text-secondary small">
                                    <span class="badge text-bg-light border">
                                        <i class="bi bi-calendar-event me-1" aria-hidden="true"></i>
                                        {{ $doacao->data_coleta->format('d/m/Y H:i') }}
                                    </span>
                                    <span class="badge text-bg-light border">
                                        <i class="bi bi-geo-alt me-1" aria-hidden="true"></i>
                                        {{ $doacao->agendamento?->campanha?->localColeta?->nome ?? 'Local indisponivel' }}
                                    </span>
                                    @if ($doacao->status === 'confirmada')
                                        <span class="badge text-bg-danger text-white border border-danger">
                                            <i class="bi bi-droplet-fill me-1" aria-hidden="true"></i>
                                            {{ $doacao->quantidade_ml }} mL
                                        </span>
                                    @endif
                                </div>
                                
                                @if ($doacao->status === 'recusada' && $doacao->motivo_recusa)
                                    <div class="mt-3 p-3 bg-light rounded text-secondary small border">
                                        <strong>Motivo clinico da recusa:</strong> {{ $doacao->motivo_recusa }}
                                    </div>
                                @endif
                                
                                @if ($doacao->bolsaSangue && $doacao->status === 'confirmada')
                                    <div class="mt-2 text-secondary small">
                                        <i class="bi bi-box-seam me-1"></i> Bolsa gerada: 
                                        <strong>{{ ucfirst($doacao->bolsaSangue->status) }}</strong>
                                        (Validade: {{ $doacao->bolsaSangue->validade_em->format('d/m/Y') }})
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="border rounded-3 p-4 text-center">
                        <h3 class="h6 fw-bold mb-1">Nenhum historico encontrado</h3>
                        <p class="text-secondary mb-3">Quando voce realizar sua primeira doacao confirmada, o resultado aparecera aqui.</p>
                        <a class="btn btn-primary d-inline-flex align-items-center gap-2" href="{{ route('home') }}">
                            Ver campanhas
                            <i class="bi bi-arrow-right-short" aria-hidden="true"></i>
                        </a>
                    </div>
                @endforelse

                @if ($doacoes->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $doacoes->links() }}
                    </div>
                @endif
            </div>
        </article>
    </section>
</x-layouts.public>

<x-layouts.public title="Meus agendamentos">
    @php
        $statusLabels = [
            'agendado' => 'Agendado',
            'cancelado' => 'Cancelado',
            'realizado' => 'Realizado',
            'faltou' => 'Faltou',
        ];
        $statusClasses = [
            'agendado' => 'text-bg-primary',
            'cancelado' => 'text-bg-secondary',
            'realizado' => 'text-bg-success',
            'faltou' => 'text-bg-warning',
        ];
    @endphp

    <section class="bg-white border-bottom">
        <div class="container py-5">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <span class="badge text-bg-light border mb-3">
                        <i class="bi bi-heart-pulse me-1" aria-hidden="true"></i>
                        Area do doador
                    </span>
                    <h1 class="h2 fw-bold mb-2">Meus agendamentos</h1>
                    <p class="text-secondary mb-0">
                        Consulte suas proximas doacoes e gerencie horarios ainda ativos.
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
        <article class="card shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                    <div>
                        <h2 class="h5 fw-bold mb-1">Proximos agendamentos</h2>
                        <p class="text-secondary mb-0">Agendamentos que ainda podem ser acompanhados pelo doador.</p>
                    </div>

                    <span class="badge text-bg-light border align-self-start">
                        <i class="bi bi-calendar-check me-1" aria-hidden="true"></i>
                        {{ $agendamentosAtivos->count() }} {{ $agendamentosAtivos->count() === 1 ? 'ativo' : 'ativos' }}
                    </span>
                </div>

                @forelse ($agendamentosAtivos as $agendamento)
                    <div class="border rounded-3 p-3 mb-3">
                        <div class="d-flex flex-column flex-xl-row justify-content-between gap-3">
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                    <h3 class="h6 fw-bold mb-0">{{ $agendamento->campanha?->titulo ?? 'Campanha indisponivel' }}</h3>
                                    <span class="badge {{ $statusClasses[$agendamento->status] ?? 'text-bg-light' }}">
                                        {{ $statusLabels[$agendamento->status] ?? $agendamento->status }}
                                    </span>
                                </div>

                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge text-bg-light border">
                                        <i class="bi bi-calendar-event me-1" aria-hidden="true"></i>
                                        {{ $agendamento->data_hora->format('d/m/Y') }}
                                    </span>
                                    <span class="badge text-bg-light border">
                                        <i class="bi bi-clock me-1" aria-hidden="true"></i>
                                        {{ $agendamento->data_hora->format('H:i') }}
                                    </span>
                                    <span class="badge text-bg-light border">
                                        <i class="bi bi-geo-alt me-1" aria-hidden="true"></i>
                                        {{ $agendamento->campanha?->localColeta?->nome ?? 'Local indisponivel' }}
                                    </span>
                                </div>
                            </div>

                            <div class="d-grid d-sm-flex flex-sm-nowrap flex-shrink-0 align-items-start justify-content-sm-end gap-2">
                                <a class="btn btn-outline-primary d-inline-flex align-items-center justify-content-center gap-2" href="{{ route('usuario.agendamentos.show', $agendamento) }}">
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                    Ver
                                </a>
                                <a class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-2" href="{{ route('usuario.agendamentos.edit', $agendamento) }}">
                                    <i class="bi bi-calendar2-week" aria-hidden="true"></i>
                                    Reagendar
                                </a>
                                <form
                                    class="d-grid m-0"
                                    method="POST"
                                    action="{{ route('usuario.agendamentos.cancel', $agendamento) }}"
                                    data-confirm-title="Cancelar agendamento?"
                                    data-confirm-text="O horario sera liberado para outros doadores. Use reagendar se quiser apenas trocar de horario."
                                    data-confirm-button-text="Cancelar agendamento"
                                    data-confirm-button-color="#c62828"
                                >
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-outline-danger d-inline-flex align-items-center justify-content-center gap-2" type="submit">
                                        <i class="bi bi-x-circle" aria-hidden="true"></i>
                                        Cancelar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="border rounded-3 p-4 text-center">
                        <h3 class="h6 fw-bold mb-1">Nenhum agendamento ativo</h3>
                        <p class="text-secondary mb-3">Quando voce escolher uma campanha, o horario aparecera aqui.</p>
                        <a class="btn btn-primary d-inline-flex align-items-center gap-2" href="{{ route('home') }}">
                            Ver campanhas
                            <i class="bi bi-arrow-right-short" aria-hidden="true"></i>
                        </a>
                    </div>
                @endforelse
            </div>
        </article>

        <article class="card shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                    <div>
                        <h2 class="h5 fw-bold mb-1">Historico</h2>
                        <p class="text-secondary mb-0">Agendamentos cancelados, realizados, ausentes ou vencidos.</p>
                    </div>
                </div>

                @forelse ($agendamentosHistorico as $agendamento)
                    <div class="border rounded-3 p-3 mb-3">
                        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                            <div>
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                    <h3 class="h6 fw-bold mb-0">{{ $agendamento->campanha?->titulo ?? 'Campanha indisponivel' }}</h3>
                                    <span class="badge {{ $statusClasses[$agendamento->status] ?? 'text-bg-light' }}">
                                        {{ $statusLabels[$agendamento->status] ?? $agendamento->status }}
                                    </span>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge text-bg-light border">
                                        <i class="bi bi-calendar-event me-1" aria-hidden="true"></i>
                                        {{ $agendamento->data_hora->format('d/m/Y H:i') }}
                                    </span>
                                    <span class="badge text-bg-light border">
                                        <i class="bi bi-geo-alt me-1" aria-hidden="true"></i>
                                        {{ $agendamento->campanha?->localColeta?->nome ?? 'Local indisponivel' }}
                                    </span>
                                </div>
                            </div>

                            <a class="btn btn-outline-primary d-inline-flex align-items-center justify-content-center gap-2 align-self-lg-start" href="{{ route('usuario.agendamentos.show', $agendamento) }}">
                                <i class="bi bi-eye" aria-hidden="true"></i>
                                Ver detalhes
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="border rounded-3 p-4 text-center">
                        <h3 class="h6 fw-bold mb-1">Nenhum historico encontrado</h3>
                        <p class="text-secondary mb-0">Agendamentos finalizados aparecerao aqui.</p>
                    </div>
                @endforelse

                @if ($agendamentosHistorico->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $agendamentosHistorico->links() }}
                    </div>
                @endif
            </div>
        </article>
    </section>
</x-layouts.public>

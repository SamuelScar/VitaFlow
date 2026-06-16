<x-layouts.public title="Detalhes do agendamento">
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
        $podeGerenciar = $agendamento->podeSerGerenciadoPeloDoador();
    @endphp

    <section class="bg-white border-bottom">
        <div class="container py-5">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <span class="badge text-bg-light border mb-3">
                        <i class="bi bi-calendar-check me-1" aria-hidden="true"></i>
                        Agendamento
                    </span>
                    <h1 class="h2 fw-bold mb-2">{{ $agendamento->campanha?->titulo ?? 'Campanha indisponivel' }}</h1>
                    <p class="text-secondary mb-0">
                        Detalhes do horario escolhido para doacao de sangue.
                    </p>
                </div>

                <a class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-2" href="{{ route('usuario.agendamentos.index') }}">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    Voltar
                </a>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="border rounded-3 p-3 h-100">
                    <span class="text-secondary small fw-semibold text-uppercase">Status</span>
                    <p class="fw-semibold mb-0">
                        <span class="badge {{ $statusClasses[$agendamento->status] ?? 'text-bg-light' }}">
                            {{ $statusLabels[$agendamento->status] ?? $agendamento->status }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="border rounded-3 p-3 h-100">
                    <span class="text-secondary small fw-semibold text-uppercase">Data</span>
                    <p class="fw-semibold mb-0">
                        <i class="bi bi-calendar-event me-1 text-primary" aria-hidden="true"></i>
                        {{ $agendamento->data_hora->format('d/m/Y') }}
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="border rounded-3 p-3 h-100">
                    <span class="text-secondary small fw-semibold text-uppercase">Horario</span>
                    <p class="fw-semibold mb-0">
                        <i class="bi bi-clock me-1 text-primary" aria-hidden="true"></i>
                        {{ $agendamento->data_hora->format('H:i') }}
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="border rounded-3 p-3 h-100">
                    <span class="text-secondary small fw-semibold text-uppercase">Local</span>
                    <p class="fw-semibold mb-0">
                        <i class="bi bi-geo-alt me-1 text-primary" aria-hidden="true"></i>
                        {{ $agendamento->campanha?->localColeta?->nome ?? 'Local indisponivel' }}
                    </p>
                </div>
            </div>
        </div>

        <article class="card shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-4">
                    <div class="flex-grow-1">
                        <h2 class="h5 fw-bold mb-2">Dados da campanha</h2>
                        <p class="text-secondary mb-3">
                            {{ $agendamento->campanha?->descricao ?? 'As informacoes desta campanha nao estao disponiveis.' }}
                        </p>

                        @if ($agendamento->doacao)
                            <div class="border rounded-3 p-3">
                                <h3 class="h6 fw-bold mb-2">Doacao registrada</h3>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge text-bg-light border">
                                        <i class="bi bi-calendar-heart me-1" aria-hidden="true"></i>
                                        {{ $agendamento->doacao->data_coleta->format('d/m/Y H:i') }}
                                    </span>
                                    <span class="badge text-bg-light border">
                                        <i class="bi bi-droplet-half me-1" aria-hidden="true"></i>
                                        {{ $agendamento->doacao->quantidade_ml ?? 0 }} ml
                                    </span>
                                    <span class="badge {{ $agendamento->doacao->status === 'confirmada' ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $agendamento->doacao->status === 'confirmada' ? 'Confirmada' : 'Recusada' }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="border rounded-3 p-3">
                                <h3 class="h6 fw-bold mb-1">Sem doacao registrada</h3>
                                <p class="text-secondary mb-0">
                                    A doacao sera vinculada ao agendamento quando o comparecimento for registrado pela equipe.
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="d-grid gap-2 align-self-lg-start">
                        @if ($podeGerenciar)
                            <a class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-2" href="{{ route('usuario.agendamentos.edit', $agendamento) }}">
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
                        @else
                            <span class="badge text-bg-light border">
                                <i class="bi bi-lock me-1" aria-hidden="true"></i>
                                Sem acoes disponiveis
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </article>
    </section>
</x-layouts.public>

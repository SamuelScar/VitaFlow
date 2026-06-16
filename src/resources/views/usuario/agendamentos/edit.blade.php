<x-layouts.public title="Reagendar doacao">
    @php
        $tiposAlvo = $campanha->tipos_sanguineos_alvo;
        $tiposAlvo = is_array($tiposAlvo) && count($tiposAlvo) > 0
            ? implode(', ', $tiposAlvo)
            : 'Todos os tipos';
        $horarioInicio = substr((string) $campanha->horario_inicio, 0, 5);
        $horarioFim = substr((string) $campanha->horario_fim, 0, 5);
    @endphp

    <section class="bg-white border-bottom">
        <div class="container py-5">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-4">
                <div class="col-lg-8">
                    <span class="badge text-bg-light border mb-3">
                        <i class="bi bi-calendar2-week me-1" aria-hidden="true"></i>
                        Reagendamento de doacao
                    </span>
                    <h1 class="h2 fw-bold mb-3">{{ $campanha->titulo }}</h1>
                    <p class="text-secondary mb-0">
                        Horario atual: {{ $agendamento->data_hora->format('d/m/Y') }} as {{ $agendamento->data_hora->format('H:i') }}.
                    </p>
                </div>

                <div class="d-grid gap-2 align-self-lg-start">
                    <a class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-2" href="{{ route('usuario.agendamentos.show', $agendamento) }}">
                        <i class="bi bi-arrow-left" aria-hidden="true"></i>
                        Voltar
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="border rounded-3 p-3 h-100">
                    <span class="text-secondary small fw-semibold text-uppercase">Local</span>
                    <p class="fw-semibold mb-0">
                        <i class="bi bi-geo-alt me-1 text-primary" aria-hidden="true"></i>
                        {{ $campanha->localColeta?->nome ?? 'Local de coleta indisponivel' }}
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="border rounded-3 p-3 h-100">
                    <span class="text-secondary small fw-semibold text-uppercase">Periodo</span>
                    <p class="fw-semibold mb-0">
                        <i class="bi bi-calendar-event me-1 text-primary" aria-hidden="true"></i>
                        {{ $campanha->data_inicio->format('d/m/Y') }} a {{ $campanha->data_fim->format('d/m/Y') }}
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="border rounded-3 p-3 h-100">
                    <span class="text-secondary small fw-semibold text-uppercase">Atendimento</span>
                    <p class="fw-semibold mb-0">
                        <i class="bi bi-clock me-1 text-primary" aria-hidden="true"></i>
                        {{ $horarioInicio }} as {{ $horarioFim }}
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="border rounded-3 p-3 h-100">
                    <span class="text-secondary small fw-semibold text-uppercase">Capacidade</span>
                    <p class="fw-semibold mb-1">
                        <i class="bi bi-people me-1 text-primary" aria-hidden="true"></i>
                        {{ $campanha->agendamentos_por_horario }} por horario
                    </p>
                    <span class="text-secondary small">{{ $totalAgendamentos }} {{ $totalAgendamentos === 1 ? 'agendamento ativo' : 'agendamentos ativos' }}</span>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 mb-4">
            <span class="badge text-bg-light border">
                <i class="bi bi-droplet-half me-1" aria-hidden="true"></i>
                Tipos alvo: {{ $tiposAlvo }}
            </span>
            <span class="badge text-bg-light border">
                <i class="bi bi-bullseye me-1" aria-hidden="true"></i>
                Meta de {{ $campanha->meta_bolsas }} {{ $campanha->meta_bolsas === 1 ? 'bolsa' : 'bolsas' }}
            </span>
        </div>

        @include('usuario.agendamentos.partials.form', [
            'action' => route('usuario.agendamentos.update', $agendamento),
            'method' => 'PUT',
            'cancelUrl' => route('usuario.agendamentos.show', $agendamento),
            'cancelLabel' => 'Voltar',
            'submitLabel' => 'Salvar reagendamento',
            'submitIcon' => 'bi-calendar2-check',
            'selectedDataHora' => $agendamento->data_hora->format('Y-m-d\TH:i'),
            'confirmTitle' => 'Confirmar reagendamento?',
            'confirmDefaultText' => 'Revise o novo horario antes de confirmar.',
            'confirmButtonText' => 'Salvar reagendamento',
            'emptyMessage' => 'Nenhum horario disponivel para reagendar esta campanha.',
        ])
    </section>
</x-layouts.public>

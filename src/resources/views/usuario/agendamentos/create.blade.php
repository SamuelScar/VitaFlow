<x-layouts.public title="Agendar doacao">
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
                        <i class="bi bi-calendar-plus me-1" aria-hidden="true"></i>
                        Agendamento de doacao
                    </span>
                    <h1 class="h2 fw-bold mb-3">{{ $campanha->titulo }}</h1>
                    <p class="text-secondary mb-0">{{ $campanha->descricao }}</p>
                </div>

                <div class="d-grid gap-2 align-self-lg-start">
                    <a class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-2" href="{{ route('home') }}">
                        <i class="bi bi-arrow-left" aria-hidden="true"></i>
                        Ver campanhas
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

        <form
            method="POST"
            action="{{ route('usuario.agendamentos.store', $campanha) }}"
            class="border rounded-3 p-3 p-lg-4"
            data-agendamento-picker
            data-horarios='@json($horarios)'
            data-horario-inicio="{{ $horarioInicio }}:00"
            data-horario-fim="{{ $horarioFim }}:00"
            data-confirm-title="Confirmar agendamento?"
            data-confirm-text="Revise o horario escolhido antes de confirmar."
            data-confirm-default-text="Revise o horario escolhido antes de confirmar."
            data-confirm-button-text="Confirmar agendamento"
            data-confirm-button-color="#c62828"
            novalidate
        >
            @csrf

            <input
                data-agendamento-valor
                type="hidden"
                name="data_hora"
                value="{{ old('data_hora') }}"
            >

            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                <div>
                    <h2 class="h4 fw-bold mb-2">Escolha no calendario</h2>
                    <p class="text-secondary mb-0">
                        Clique em um horario disponivel para selecionar seu agendamento.
                    </p>
                </div>

                <div class="border rounded-3 px-3 py-2 appointment-selection-summary" data-agendamento-resumo>
                    <span class="text-secondary small fw-semibold text-uppercase">Selecionado</span>
                    <p class="fw-semibold mb-0" data-agendamento-selecionado>Nenhum horario selecionado</p>
                    <span class="text-secondary small" data-agendamento-ajuda>Clique em um horario disponivel no calendario.</span>
                </div>
            </div>

            <div class="appointment-selection-tray mb-3" data-agendamento-resumo>
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-start gap-3">
                        <span class="appointment-selection-icon" aria-hidden="true">
                            <i class="bi bi-calendar-check"></i>
                        </span>
                        <div>
                            <span class="text-secondary small fw-semibold text-uppercase">Horario em selecao</span>
                            <p class="fw-semibold mb-0" data-agendamento-selecionado>Nenhum horario selecionado</p>
                            <span class="text-secondary small" data-agendamento-ajuda>Clique em um horario disponivel no calendario.</span>
                        </div>
                    </div>
                    <span class="badge text-bg-light border appointment-selection-status" data-agendamento-status>
                        Selecione um horario
                    </span>
                </div>
            </div>

            <div
                class="appointment-calendar @error('data_hora') is-invalid @enderror"
                data-agendamento-calendario
            ></div>

            @error('data_hora')
                <div class="invalid-feedback d-block mt-3">{{ $message }}</div>
            @else
                <div class="form-text mt-3">
                    Horarios lotados aparecem bloqueados. Use a visualizacao de semana ou dia para ver a agenda com mais detalhe.
                </div>
            @enderror

            <noscript>
                <div class="mt-4">
                    <label class="form-label fw-semibold" for="data_hora_fallback">Data e horario</label>
                    <select
                        class="form-select form-select-lg"
                        id="data_hora_fallback"
                        name="data_hora"
                        required
                    >
                        <option value="">Selecione um horario</option>
                        @foreach ($horarios as $horario)
                            <option
                                value="{{ $horario['valor'] }}"
                                @selected(old('data_hora') === $horario['valor'])
                                @disabled($horario['lotado'])
                            >
                                {{ $horario['grupo'] }} {{ $horario['rotulo'] }} - {{ $horario['lotado'] ? 'lotado' : "{$horario['vagas']} vagas" }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </noscript>

            @if (count($horarios) === 0)
                <div class="border border-warning-subtle bg-warning-subtle rounded-3 p-3 mt-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle text-warning-emphasis" aria-hidden="true"></i>
                        <span class="text-warning-emphasis">Nenhum horario disponivel para esta campanha.</span>
                    </div>
                </div>
            @endif

            <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-4">
                <a class="btn btn-outline-secondary btn-lg d-inline-flex align-items-center justify-content-center gap-2" href="{{ route('home') }}">
                    Cancelar
                </a>
                <button class="btn btn-primary btn-lg d-inline-flex align-items-center justify-content-center gap-2" type="submit" data-agendamento-submit disabled>
                    <i class="bi bi-calendar-check" aria-hidden="true"></i>
                    Confirmar agendamento
                </button>
            </div>
        </form>
    </section>
</x-layouts.public>

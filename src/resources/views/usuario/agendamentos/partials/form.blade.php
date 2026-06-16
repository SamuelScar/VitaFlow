@php
    $method = strtoupper($method ?? 'POST');
    $selectedDataHora = old('data_hora', $selectedDataHora ?? null);
    $submitLabel = $submitLabel ?? 'Confirmar agendamento';
    $submitIcon = $submitIcon ?? 'bi-calendar-check';
    $cancelUrl = $cancelUrl ?? route('home');
    $cancelLabel = $cancelLabel ?? 'Cancelar';
    $confirmTitle = $confirmTitle ?? 'Confirmar agendamento?';
    $confirmDefaultText = $confirmDefaultText ?? 'Revise o horario escolhido antes de confirmar.';
    $confirmButtonText = $confirmButtonText ?? $submitLabel;
    $emptyMessage = $emptyMessage ?? 'Nenhum horario disponivel para esta campanha.';
@endphp

<form
    method="POST"
    action="{{ $action }}"
    class="border rounded-3 p-3 p-lg-4"
    data-agendamento-picker
    data-horarios='@json($horarios)'
    data-horario-inicio="{{ $horarioInicio }}:00"
    data-horario-fim="{{ $horarioFim }}:00"
    data-confirm-title="{{ $confirmTitle }}"
    data-confirm-text="{{ $confirmDefaultText }}"
    data-confirm-default-text="{{ $confirmDefaultText }}"
    data-confirm-button-text="{{ $confirmButtonText }}"
    data-confirm-button-color="#c62828"
    novalidate
>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <input
        data-agendamento-valor
        type="hidden"
        name="data_hora"
        value="{{ $selectedDataHora }}"
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
            Horarios lotados ou bloqueados aparecem indisponiveis. Use a visualizacao de semana ou dia para ver a agenda com mais detalhe.
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
                        @selected($selectedDataHora === $horario['valor'])
                        @disabled($horario['lotado'] || $horario['bloqueado'])
                    >
                        {{ $horario['grupo'] }} {{ $horario['rotulo'] }} - {{ $horario['lotado'] ? 'lotado' : ($horario['bloqueado'] ? ($horario['motivo'] ?? 'indisponivel') : "{$horario['vagas']} vagas") }}
                    </option>
                @endforeach
            </select>
        </div>
    </noscript>

    @if (count($horarios) === 0)
        <div class="border border-warning-subtle bg-warning-subtle rounded-3 p-3 mt-4">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle text-warning-emphasis" aria-hidden="true"></i>
                <span class="text-warning-emphasis">{{ $emptyMessage }}</span>
            </div>
        </div>
    @endif

    <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-4">
        <a class="btn btn-outline-secondary btn-lg d-inline-flex align-items-center justify-content-center gap-2" href="{{ $cancelUrl }}">
            {{ $cancelLabel }}
        </a>
        <button class="btn btn-primary btn-lg d-inline-flex align-items-center justify-content-center gap-2" type="submit" data-agendamento-submit disabled>
            <i class="bi {{ $submitIcon }}" aria-hidden="true"></i>
            {{ $submitLabel }}
        </button>
    </div>
</form>

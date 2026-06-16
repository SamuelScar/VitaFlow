@props(['campanha'])

@php
    $tiposAlvo = $campanha->tipos_sanguineos_alvo;
    $tiposAlvo = is_array($tiposAlvo) && count($tiposAlvo) > 0
        ? implode(', ', $tiposAlvo)
        : 'Todos os tipos';
    $usuario = auth()->user();
    $usuarioAgendado = $usuario?->isDoador() && (bool) ($campanha->usuario_agendado ?? false);
    $linkAgendamento = $usuario?->isAdmin()
        ? route('admin.campanhas.show', $campanha)
        : ($usuarioAgendado
            ? route('usuario.agendamentos.index')
            : route('usuario.agendamentos.create', $campanha));
    $rotuloAcao = $usuario?->isAdmin()
        ? 'Ver campanha'
        : ($usuarioAgendado
            ? 'Ver meu agendamento'
            : 'Agendar doacao');
    $iconeAcao = $usuario?->isAdmin()
        ? 'bi-eye'
        : ($usuarioAgendado
            ? 'bi-calendar-check'
            : 'bi-calendar-plus');
@endphp

<article class="card h-100 shadow-sm rounded-3">
    <div class="card-body p-4 d-flex flex-column">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
            <h3 class="h5 mb-0">{{ $campanha->titulo }}</h3>
            <span class="badge text-bg-success">
                <i class="bi bi-check-circle me-1" aria-hidden="true"></i>
                Ativa
            </span>
        </div>

        @if ($usuarioAgendado)
            <span class="badge text-bg-primary align-self-start mb-3">
                <i class="bi bi-calendar-check me-1" aria-hidden="true"></i>
                Voce ja esta cadastrado nesta campanha
            </span>
        @endif

        <p class="text-secondary">{{ $campanha->descricao }}</p>

        <div class="mt-auto">
            <div class="d-grid gap-2 small text-secondary mb-3">
                <span>
                    <i class="bi bi-geo-alt me-1" aria-hidden="true"></i>
                    {{ $campanha->localColeta?->nome ?? 'Local de coleta indisponivel' }}
                </span>
                <span>
                    <i class="bi bi-bullseye me-1" aria-hidden="true"></i>
                    Meta de {{ $campanha->meta_bolsas }} {{ $campanha->meta_bolsas === 1 ? 'bolsa' : 'bolsas' }}
                </span>
                <span>
                    <i class="bi bi-people me-1" aria-hidden="true"></i>
                    {{ $campanha->agendamentos_por_horario }} por horario
                </span>
                <span>
                    <i class="bi bi-clock me-1" aria-hidden="true"></i>
                    {{ substr((string) $campanha->horario_inicio, 0, 5) }} as {{ substr((string) $campanha->horario_fim, 0, 5) }}
                </span>
                <span>
                    <i class="bi bi-droplet-half me-1" aria-hidden="true"></i>
                    {{ $tiposAlvo }}
                </span>
                <span>
                    <i class="bi bi-calendar-event me-1" aria-hidden="true"></i>
                    Ate {{ $campanha->data_fim->format('d/m/Y') }}
                </span>
            </div>

            <a class="btn btn-outline-primary w-100 d-inline-flex align-items-center justify-content-center gap-2" href="{{ $linkAgendamento }}">
                <i class="bi {{ $iconeAcao }}" aria-hidden="true"></i>
                {{ $rotuloAcao }}
            </a>
        </div>
    </div>
</article>

@props(['campanha'])

@php
    $tiposAlvo = $campanha->tipos_sanguineos_alvo;
    $tiposAlvo = is_array($tiposAlvo) && count($tiposAlvo) > 0
        ? implode(', ', $tiposAlvo)
        : 'Todos os tipos';
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
                    <i class="bi bi-droplet-half me-1" aria-hidden="true"></i>
                    {{ $tiposAlvo }}
                </span>
                <span>
                    <i class="bi bi-calendar-event me-1" aria-hidden="true"></i>
                    Ate {{ $campanha->data_fim->format('d/m/Y') }}
                </span>
            </div>

            <a class="btn btn-outline-primary w-100 d-inline-flex align-items-center justify-content-center gap-2" href="{{ auth()->check() ? route('dashboard') : route('login') }}">
                <i class="bi bi-calendar-plus" aria-hidden="true"></i>
                Entrar para participar
            </a>
        </div>
    </div>
</article>

@props([
    'backHref' => null,
    'backLabel' => 'Voltar',
    'description' => '',
    'icon' => 'bi-shield-check',
    'label',
    'title',
])

<section class="bg-white border-bottom">
    <div class="container py-5">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <span class="badge text-bg-light border mb-3">
                    <i class="bi {{ $icon }} me-1" aria-hidden="true"></i>
                    {{ $label }}
                </span>
                <h1 class="h2 fw-bold mb-2">{{ $title }}</h1>
                @if ($description !== '')
                    <p class="text-secondary mb-0">{{ $description }}</p>
                @endif
            </div>

            @if ($backHref)
                <a class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" href="{{ $backHref }}">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    {{ $backLabel }}
                </a>
            @endif
        </div>
    </div>
</section>

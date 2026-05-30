@props([
    'title',
    'description',
    'href' => null,
    'buttonLabel' => 'Em breve',
    'icon' => null,
])

<article class="card h-100 shadow-sm rounded-3">
    <div class="card-body p-4 d-flex flex-column">
        @if ($icon)
            <span class="dashboard-icon d-inline-flex align-items-center justify-content-center rounded bg-primary-subtle text-primary mb-3">
                <i class="bi {{ $icon }}" aria-hidden="true"></i>
            </span>
        @endif

        <h2 class="h5">{{ $title }}</h2>
        <p class="text-secondary mb-4">{{ $description }}</p>

        <div class="mt-auto">
            @if ($href)
                <a class="btn btn-primary d-inline-flex align-items-center gap-2" href="{{ $href }}">
                    {{ $buttonLabel }}
                    <i class="bi bi-arrow-right-short" aria-hidden="true"></i>
                </a>
            @else
                <button class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" type="button" disabled>
                    <i class="bi bi-clock" aria-hidden="true"></i>
                    {{ $buttonLabel }}
                </button>
            @endif
        </div>
    </div>
</article>

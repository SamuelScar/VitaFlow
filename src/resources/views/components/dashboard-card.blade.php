@props([
    'title',
    'description',
    'href' => null,
    'buttonLabel' => 'Em breve',
])

<article class="card h-100 shadow-sm rounded-3">
    <div class="card-body p-4">
        <h2 class="h5">{{ $title }}</h2>
        <p class="text-secondary mb-4">{{ $description }}</p>

        @if ($href)
            <a class="btn btn-primary" href="{{ $href }}">{{ $buttonLabel }}</a>
        @else
            <button class="btn btn-outline-secondary" type="button" disabled>{{ $buttonLabel }}</button>
        @endif
    </div>
</article>

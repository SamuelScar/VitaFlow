@php
    $paginaAnterior = url()->previous();
    $urlVoltar = $paginaAnterior !== url()->current() ? $paginaAnterior : route('home');
@endphp

<nav class="d-flex gap-2 mb-4" aria-label="Navegacao da autenticacao">
    <a
        class="btn btn-outline-secondary flex-fill d-inline-flex align-items-center justify-content-center gap-2"
        href="{{ $urlVoltar }}"
    >
        <i class="bi bi-arrow-left" aria-hidden="true"></i>
        Voltar
    </a>

    <a class="btn btn-outline-primary flex-fill d-inline-flex align-items-center justify-content-center gap-2" href="{{ route('home') }}">
        <i class="bi bi-house" aria-hidden="true"></i>
        Home
    </a>
</nav>

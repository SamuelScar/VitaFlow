@props(['campanha'])

<article class="card h-100 shadow-sm rounded-3">
    <div class="card-body p-4 d-flex flex-column">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
            <h3 class="h5 mb-0">{{ $campanha['titulo'] }}</h3>
            <span class="badge text-bg-success">{{ $campanha['status'] }}</span>
        </div>

        <p class="text-secondary">{{ $campanha['descricao'] }}</p>

        <div class="mt-auto">
            <div class="d-flex justify-content-between small text-secondary mb-2">
                <span>{{ $campanha['resultado'] }}</span>
                <span>Meta {{ $campanha['meta'] }}</span>
            </div>

            <div
                class="progress mb-3"
                role="progressbar"
                aria-label="Progresso da campanha de sangue"
                aria-valuenow="{{ $campanha['progresso'] }}"
                aria-valuemin="0"
                aria-valuemax="100"
            >
                <div class="progress-bar bg-primary" style="width: {{ $campanha['progresso'] }}%"></div>
            </div>

            <a class="btn btn-outline-primary w-100" href="{{ route('login') }}">Entrar para agendar</a>
        </div>
    </div>
</article>

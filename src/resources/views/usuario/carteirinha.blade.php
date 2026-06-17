<x-layouts.public title="Carteirinha de doador">
    @php
        $usuario = auth()->user();
        $carteira = $usuario->carteiraDoacao;
        $tiposSanguineos = App\Support\TipoSanguineo::values();
    @endphp

    <section class="bg-white border-bottom">
        <div class="container py-5">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <span class="badge text-bg-light border mb-3">
                        <i class="bi bi-person-vcard me-1" aria-hidden="true"></i>
                        Carteirinha de doador
                    </span>
                    <h1 class="h2 fw-bold mb-2">
                        {{ $carteira ? 'Sua carteirinha' : 'Emitir carteirinha' }}
                    </h1>
                    <p class="text-secondary mb-0">
                        {{ $carteira ? 'Confira seus dados cadastrados para doacao de sangue.' : 'Preencha seus dados para participar das campanhas de doacao de sangue.' }}
                    </p>
                </div>

                <a class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" href="{{ route('usuario.dashboard') }}">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    Voltar
                </a>
            </div>
        </div>
    </section>

    <section class="container py-5">
        @if ($carteira)
            @include('usuario.partials.carteirinha-card')
        @elseif (!$usuario->hasVerifiedEmail())
            <div class="card shadow-sm rounded-3 border-warning">
                <div class="card-body p-4 p-lg-5 text-center">
                    <i class="bi bi-envelope-exclamation text-warning" style="font-size: 3rem;"></i>
                    <h2 class="h4 mt-3 mb-2">Verificação de e-mail necessária</h2>
                    <p class="text-secondary mb-4">Para emitir sua carteirinha de doador, você precisa verificar seu endereço de e-mail primeiro.</p>
                    <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning">Reenviar e-mail de verificação</button>
                    </form>
                </div>
            </div>
        @else
            <div class="card shadow-sm rounded-3">
                <div class="card-body p-4 p-lg-5">
                    @include('usuario.partials.carteirinha-form', [
                        'action' => route('usuario.carteirinha.store'),
                        'submitLabel' => 'Emitir carteirinha',
                        'idPrefix' => 'emitir_carteirinha',
                    ])
                </div>
            </div>
        @endif
    </section>
</x-layouts.public>

<x-layouts.guest title="Recuperar senha">
    <section class="card login-card w-100 shadow-sm">
        <div class="card-body p-4 p-sm-5">
            <header class="mb-4">
                <span class="brand d-inline-flex align-items-center justify-content-center rounded bg-primary text-white fw-bold mb-3">VF</span>
                <h1 class="h3 mb-2">
                    <i class="bi bi-key me-2" aria-hidden="true"></i>
                    Recuperar senha
                </h1>
                <p class="text-secondary mb-0">Informe seu e-mail para receber o link de redefinicao.</p>
            </header>

            <form method="POST" action="{{ route('password.email') }}" data-validate-form>
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-semibold" for="email">E-mail</label>
                    <input
                        class="form-control"
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        placeholder="seu@email.com"
                        autocomplete="email"
                        required
                    >
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2" type="submit">
                    <i class="bi bi-envelope-check" aria-hidden="true"></i>
                    Enviar link
                </button>
            </form>

            <p class="text-secondary small text-center mb-0 mt-4">
                Lembrou a senha?
                <a href="{{ route('login') }}">Entrar</a>
            </p>
        </div>
    </section>
</x-layouts.guest>

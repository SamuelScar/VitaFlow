<x-layouts.guest title="Login">
    <section class="card login-card w-100 shadow-sm">
        <div class="card-body p-4 p-sm-5">
            <header class="mb-4">
                <span class="brand d-inline-flex align-items-center justify-content-center rounded bg-primary text-white fw-bold mb-3">VF</span>
                <h1 class="h3 mb-2">
                    <i class="bi bi-box-arrow-in-right me-2" aria-hidden="true"></i>
                    Entrar
                </h1>
                <p class="text-secondary mb-0">Acesse sua conta {{ config('app.name') }}.</p>
            </header>

            <form method="POST" action="{{ route('login.store') }}" data-validate-form>
                @csrf

                <div class="mb-3">
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

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="password">Senha</label>
                    <input
                        class="form-control"
                        id="password"
                        name="password"
                        type="password"
                        placeholder="Sua senha"
                        autocomplete="current-password"
                        required
                    >
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2" type="submit">
                    <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                    Acessar
                </button>
            </form>

            <p class="text-secondary small text-center mb-0 mt-4">
                Ainda nao tem uma conta?
                <a href="{{ route('register') }}">Criar conta</a>
            </p>
        </div>
    </section>
</x-layouts.guest>

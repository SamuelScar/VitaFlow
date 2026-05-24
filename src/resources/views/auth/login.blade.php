<x-layouts.guest title="Login">
    <section class="card login-card w-100">
        <div class="card-body p-4 p-sm-5">
            <header class="mb-4">
                <span class="brand d-inline-flex align-items-center justify-content-center rounded text-white fw-bold mb-3">VF</span>
                <h1 class="h3 mb-2">Entrar</h1>
                <p class="text-secondary mb-0">Acesse sua conta {{ config('app.name') }}.</p>
            </header>

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="email">E-mail</label>
                    <input
                        class="form-control"
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
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
                        autocomplete="current-password"
                        required
                    >
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <label class="form-check mb-4" for="remember">
                    <input
                        class="form-check-input"
                        id="remember"
                        name="remember"
                        type="checkbox"
                        value="1"
                        @checked(old('remember'))
                    >
                    <span class="form-check-label">Manter conectado</span>
                </label>

                <button class="btn btn-primary w-100" type="submit">Acessar</button>
            </form>
        </div>
    </section>
</x-layouts.guest>

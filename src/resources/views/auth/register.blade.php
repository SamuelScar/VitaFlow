<x-layouts.guest title="Cadastro">
    <section class="card login-card w-100 shadow-sm">
        <div class="card-body p-4 p-sm-5">
            <header class="mb-4">
                <span class="brand d-inline-flex align-items-center justify-content-center rounded bg-primary text-white fw-bold mb-3">VF</span>
                <h1 class="h3 mb-2">Criar conta</h1>
                <p class="text-secondary mb-0">Cadastre-se para acessar o {{ config('app.name') }}.</p>
            </header>

            <form method="POST" action="{{ route('register.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="name">Nome</label>
                    <input
                        class="form-control"
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        placeholder="Seu nome"
                        autocomplete="name"
                        required
                    >
                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

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
                        placeholder="Minimo de 8 caracteres"
                        autocomplete="new-password"
                        required
                    >
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold" for="password_confirmation">Confirmar senha</label>
                    <input
                        class="form-control"
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        placeholder="Repita sua senha"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <button class="btn btn-primary w-100" type="submit">Cadastrar</button>
            </form>

            <p class="text-secondary small text-center mb-0 mt-4">
                Ja tem uma conta?
                <a href="{{ route('login') }}">Entrar</a>
            </p>
        </div>
    </section>
</x-layouts.guest>

<x-layouts.guest title="Aceitar convite administrativo">
    <section class="card login-card w-100 shadow-sm">
        <div class="card-body p-4 p-sm-5">
            @include('auth.partials.navigation')

            <header class="mb-4">
                <img class="brand-logo mb-3" src="{{ asset('assets/images/logo-vitaflow-drop.png') }}" alt="{{ config('app.name') }}">
                <h1 class="h3 mb-2">
                    <i class="bi bi-shield-check me-2" aria-hidden="true"></i>
                    Criar conta administrativa
                </h1>
                <p class="text-secondary mb-0">Defina seus dados para aceitar o convite.</p>
            </header>

            @error('convite')
                <div class="alert alert-danger" role="alert">{{ $message }}</div>
            @enderror

            <form
                method="POST"
                action="{{ route('convites-admin.store', $token) }}"
                data-validate-form
                x-data="{
                    password: '',
                    passwordConfirmation: '',
                    validatePasswordConfirmation() {
                        this.$refs.passwordConfirmation?.setCustomValidity(
                            (this.password || this.passwordConfirmation) && this.passwordConfirmation !== this.password
                                ? 'A confirmacao deve ser igual a senha.'
                                : ''
                        );
                    },
                }"
            >
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="email">E-mail</label>
                    <input class="form-control" id="email" type="email" value="{{ $convite->email }}" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="name">Nome</label>
                    <input
                        class="form-control @error('name') is-invalid @enderror"
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        autocomplete="name"
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="password">Senha</label>
                    <input
                        class="form-control @error('password') is-invalid @enderror"
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        minlength="8"
                        x-model="password"
                        x-on:input="validatePasswordConfirmation()"
                        required
                    >
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold" for="password_confirmation">Confirmar senha</label>
                    <input
                        class="form-control"
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        x-model="passwordConfirmation"
                        x-ref="passwordConfirmation"
                        x-on:input="validatePasswordConfirmation()"
                        x-on:change="validatePasswordConfirmation()"
                        required
                    >
                </div>

                <button class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2" type="submit">
                    <i class="bi bi-shield-check" aria-hidden="true"></i>
                    Criar conta administrativa
                </button>
            </form>
        </div>
    </section>
</x-layouts.guest>

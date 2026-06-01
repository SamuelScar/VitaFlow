<x-layouts.guest title="Redefinir senha">
    <section class="card login-card w-100 shadow-sm">
        <div class="card-body p-4 p-sm-5">
            <header class="mb-4">
                <img class="brand-logo mb-3" src="{{ asset('assets/images/logo-vitaflow-drop.png') }}" alt="{{ config('app.name') }}">
                <h1 class="h3 mb-2">
                    <i class="bi bi-shield-lock me-2" aria-hidden="true"></i>
                    Redefinir senha
                </h1>
                <p class="text-secondary mb-0">Crie uma nova senha para acessar sua conta.</p>
            </header>

            <form
                method="POST"
                action="{{ route('password.update') }}"
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

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="email">E-mail</label>
                    <input
                        class="form-control"
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', $email) }}"
                        placeholder="seu@email.com"
                        autocomplete="email"
                        required
                    >
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="password">Nova senha</label>
                    <input
                        class="form-control"
                        id="password"
                        name="password"
                        type="password"
                        placeholder="Minimo de 8 caracteres"
                        autocomplete="new-password"
                        minlength="8"
                        x-model="password"
                        x-on:input="validatePasswordConfirmation()"
                        required
                    >
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold" for="password_confirmation">Confirmar nova senha</label>
                    <input
                        class="form-control"
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        placeholder="Repita sua nova senha"
                        autocomplete="new-password"
                        x-model="passwordConfirmation"
                        x-ref="passwordConfirmation"
                        x-on:input="validatePasswordConfirmation()"
                        x-on:change="validatePasswordConfirmation()"
                        required
                    >
                </div>

                <button class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2" type="submit">
                    <i class="bi bi-check-lg" aria-hidden="true"></i>
                    Redefinir senha
                </button>
            </form>
        </div>
    </section>
</x-layouts.guest>

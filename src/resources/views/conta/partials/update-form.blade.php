<form
    method="POST"
    action="{{ route('conta.update') }}"
    data-validate-form
    x-data="{
        currentPassword: '',
        newPassword: '',
        passwordConfirmation: '',
        validateCurrentPassword() {
            this.$refs.currentPassword?.setCustomValidity(
                this.newPassword && !this.currentPassword
                    ? 'Informe a senha atual para alterar a senha.'
                    : ''
            );
        },
        validatePasswordConfirmation() {
            this.$refs.passwordConfirmation?.setCustomValidity(
                (this.newPassword || this.passwordConfirmation) && this.passwordConfirmation !== this.newPassword
                    ? 'A confirmacao deve ser igual a nova senha.'
                    : ''
            );
        },
    }"
>
    @csrf
    @method('PUT')

    <div class="card-body p-4 p-lg-5">
        <div class="mb-4">
            <h2 class="h4 fw-bold mb-1">Informacoes principais</h2>
            <p class="text-secondary mb-0">Nome, e-mail e senha usados para acessar sua conta.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <x-forms.input
                    error-bag="updateConta"
                    id="name"
                    label="Nome"
                    name="name"
                    :value="old('name', $usuario->name)"
                    autocomplete="name"
                    required
                />
            </div>

            <div class="col-lg-6">
                <x-forms.input
                    error-bag="updateConta"
                    id="email"
                    label="E-mail"
                    name="email"
                    type="email"
                    :value="old('email', $usuario->email)"
                    autocomplete="email"
                    required
                />
                <div class="mt-2">
                    @if ($usuario->hasVerifiedEmail())
                        <span class="badge text-bg-success"><i class="bi bi-check-circle me-1"></i> Verificado</span>
                    @else
                        <span class="badge text-bg-warning"><i class="bi bi-exclamation-triangle me-1"></i> Não verificado</span>
                    @endif
                </div>
            </div>

            <div class="col-12">
                <hr class="my-2">
            </div>

            <div class="col-lg-4">
                <x-forms.input
                    error-bag="updateConta"
                    id="current_password"
                    label="Senha atual"
                    name="current_password"
                    type="password"
                    autocomplete="current-password"
                    x-model="currentPassword"
                    x-ref="currentPassword"
                    x-bind:required="newPassword.length > 0"
                    x-on:input="validateCurrentPassword()"
                />
            </div>

            <div class="col-lg-4">
                <x-forms.input
                    error-bag="updateConta"
                    id="password"
                    label="Nova senha"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    minlength="8"
                    x-model="newPassword"
                    x-on:input="validateCurrentPassword(); validatePasswordConfirmation()"
                />
            </div>

            <div class="col-lg-4">
                <x-forms.input
                    id="password_confirmation"
                    label="Confirmar nova senha"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    x-model="passwordConfirmation"
                    x-ref="passwordConfirmation"
                    x-on:input="validatePasswordConfirmation()"
                    x-on:change="validatePasswordConfirmation()"
                />
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <button class="btn btn-primary d-inline-flex align-items-center gap-2" type="submit">
                <i class="bi bi-check-lg" aria-hidden="true"></i>
                Salvar alteracoes
            </button>
        </div>
    </div>
</form>

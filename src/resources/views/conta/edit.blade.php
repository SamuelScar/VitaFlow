<x-layouts.public title="Dados da conta">
    @php
        $usuario = auth()->user();
    @endphp

    <section class="bg-white border-bottom">
        <div class="container py-5">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <span class="badge text-bg-light border mb-3">
                        <i class="bi bi-person-gear me-1" aria-hidden="true"></i>
                        Conta
                    </span>
                    <h1 class="h2 fw-bold mb-2">Dados da conta</h1>
                    <p class="text-secondary mb-0">
                        Atualize suas informacoes de acesso ao VitaFlow.
                    </p>
                </div>

                <a class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    Voltar
                </a>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <article class="card shadow-sm rounded-3">
            <form method="POST" action="{{ route('conta.update') }}" data-validate-form>
                @csrf
                @method('PUT')

                <div class="card-body p-4 p-lg-5">
                    <div class="mb-4">
                        <h2 class="h4 fw-bold mb-1">Informacoes principais</h2>
                        <p class="text-secondary mb-0">Nome, e-mail e senha usados para acessar sua conta.</p>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <label class="form-label" for="name">Nome</label>
                            <input
                                class="form-control @error('name', 'updateConta') is-invalid @enderror"
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name', $usuario->name) }}"
                                autocomplete="name"
                                required
                            >
                            @error('name', 'updateConta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label" for="email">E-mail</label>
                            <input
                                class="form-control @error('email', 'updateConta') is-invalid @enderror"
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email', $usuario->email) }}"
                                autocomplete="email"
                                required
                            >
                            @error('email', 'updateConta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                        </div>

                        <div class="col-lg-4">
                            <label class="form-label" for="current_password">Senha atual</label>
                            <input
                                class="form-control @error('current_password', 'updateConta') is-invalid @enderror"
                                id="current_password"
                                name="current_password"
                                type="password"
                                autocomplete="current-password"
                                data-required-with="[name='password']"
                                data-required-with-message="Informe a senha atual para alterar a senha."
                            >
                            @error('current_password', 'updateConta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-4">
                            <label class="form-label" for="password">Nova senha</label>
                            <input
                                class="form-control @error('password', 'updateConta') is-invalid @enderror"
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="new-password"
                                minlength="8"
                            >
                            @error('password', 'updateConta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-4">
                            <label class="form-label" for="password_confirmation">Confirmar nova senha</label>
                            <input
                                class="form-control"
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                data-matches-field="[name='password']"
                                data-matches-message="A confirmacao deve ser igual a nova senha."
                            >
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

            <div class="card-footer bg-white border-top p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <strong class="d-block">Zona de risco</strong>
                    <span class="text-secondary small">A exclusao da conta exige confirmacao por senha.</span>
                </div>

                <button
                    class="btn btn-outline-danger d-inline-flex align-items-center gap-2"
                    type="button"
                    data-delete-account-button
                >
                    <i class="bi bi-trash" aria-hidden="true"></i>
                    Excluir conta
                </button>
            </div>
        </article>

        <form method="POST" action="{{ route('conta.destroy') }}" class="d-none" data-delete-account-form>
            @csrf
            @method('DELETE')
            <input name="password" type="hidden" data-delete-account-password>
        </form>
    </section>

    @if ($errors->deleteConta->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.confirmAccountDeletion({
                    form: document.querySelector('[data-delete-account-form]'),
                    initialError: @json($errors->deleteConta->first('password')),
                });
            });
        </script>
    @endif
</x-layouts.public>

<x-layouts.public title="Dados da conta">
    @php
        $usuario = auth()->user();
    @endphp

    <section class="bg-white border-bottom">
        <div class="container py-5">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <span class="badge text-bg-light border mb-3">Conta</span>
                    <h1 class="h2 fw-bold mb-2">Dados da conta</h1>
                    <p class="text-secondary mb-0">
                        Atualize suas informacoes de acesso ao VitaFlow.
                    </p>
                </div>

                <a class="btn btn-outline-secondary" href="{{ route('dashboard') }}">Voltar</a>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <div class="row g-4 align-items-start">
            <div class="col-lg-8">
                <form method="POST" action="{{ route('conta.update') }}" class="card shadow-sm rounded-3" data-validate-form>
                    @csrf
                    @method('PUT')

                    <div class="card-body p-4 p-lg-5">
                        <div class="mb-4">
                            <h2 class="h4 fw-bold mb-1">Informacoes principais</h2>
                            <p class="text-secondary mb-0">Nome, e-mail e senha usados para acessar sua conta.</p>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
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

                            <div class="col-md-6">
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
                                <hr class="my-3">
                            </div>

                            <div class="col-md-6">
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

                            <div class="col-md-6">
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
                            <button class="btn btn-primary" type="submit">Salvar alteracoes</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <form method="POST" action="{{ route('conta.destroy') }}" class="card border-danger shadow-sm rounded-3" data-validate-form>
                    @csrf
                    @method('DELETE')

                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-2">Excluir conta</h2>
                        <p class="text-secondary">
                            Essa acao remove sua conta e encerra seu acesso ao sistema.
                        </p>

                        <label class="form-label" for="delete_password">Senha atual</label>
                        <input
                            class="form-control @error('password', 'deleteConta') is-invalid @enderror"
                            id="delete_password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                        >
                        @error('password', 'deleteConta')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <button class="btn btn-danger w-100 mt-4" type="submit">Excluir minha conta</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.public>

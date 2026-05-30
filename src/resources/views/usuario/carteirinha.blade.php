<x-layouts.public title="Carteirinha de doador">
    @php
        $usuario = auth()->user();
        $carteira = $usuario->carteiraDoacao;
        $tiposSanguineos = App\Support\TiposSanguineos::TODOS;
    @endphp

    <section class="dashboard-header border-bottom">
        <div class="container py-5">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <span class="badge text-bg-light border mb-3">Carteirinha de doador</span>
                    <h1 class="h2 fw-bold mb-2">
                        {{ $carteira ? 'Sua carteirinha' : 'Emitir carteirinha' }}
                    </h1>
                    <p class="text-secondary mb-0">
                        {{ $carteira ? 'Confira seus dados cadastrados para doacao de sangue.' : 'Preencha seus dados para participar das campanhas de doacao de sangue.' }}
                    </p>
                </div>

                <a class="btn btn-outline-secondary" href="{{ route('usuario.dashboard') }}">Voltar</a>
            </div>
        </div>
    </section>

    <section class="container py-5">
        @if ($carteira)
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-7">
                    <article class="donor-pass h-100">
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-4">
                            <div>
                                <span class="badge text-bg-light border mb-3">Carteirinha ativa</span>
                                <h2 class="h4 fw-bold mb-1">{{ $usuario->name }}</h2>
                                <p class="text-secondary mb-4">Doador cadastrado no VitaFlow</p>

                                <dl class="row mb-0 g-3">
                                    <div class="col-sm-6">
                                        <dt class="small text-secondary text-uppercase">CPF</dt>
                                        <dd class="fw-semibold mb-0">{{ $carteira->cpf }}</dd>
                                    </div>
                                    <div class="col-sm-6">
                                        <dt class="small text-secondary text-uppercase">Telefone</dt>
                                        <dd class="fw-semibold mb-0">{{ $carteira->telefone }}</dd>
                                    </div>
                                    <div class="col-sm-6">
                                        <dt class="small text-secondary text-uppercase">Cidade</dt>
                                        <dd class="fw-semibold mb-0">{{ $carteira->cidade }}</dd>
                                    </div>
                                    <div class="col-sm-6">
                                        <dt class="small text-secondary text-uppercase">Nascimento</dt>
                                        <dd class="fw-semibold mb-0">{{ $carteira->data_nascimento->format('d/m/Y') }}</dd>
                                    </div>
                                    <div class="col-sm-6">
                                        <dt class="small text-secondary text-uppercase">Peso</dt>
                                        <dd class="fw-semibold mb-0">{{ $carteira->peso }} kg</dd>
                                    </div>
                                    <div class="col-sm-6">
                                        <dt class="small text-secondary text-uppercase">Emitida em</dt>
                                        <dd class="fw-semibold mb-0">{{ $carteira->emitida_em->format('d/m/Y') }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="blood-type-box align-self-start">
                                <span class="small text-secondary text-uppercase d-block">Tipo sanguineo</span>
                                <strong>{{ $carteira->tipo_sanguineo }}</strong>
                            </div>
                        </div>
                    </article>
                </div>

                <div class="col-lg-5">
                    <article class="card dashboard-card h-100">
                        <div class="card-body p-4">
                            <h2 class="h5">Status da carteirinha</h2>
                            <p class="text-secondary mb-4">
                                Sua carteirinha esta pronta para ser usada nos proximos fluxos de agendamento.
                            </p>
                            <span class="badge status-ativa text-uppercase">{{ $carteira->status }}</span>
                        </div>
                    </article>
                </div>
            </div>
        @else
            <div class="card dashboard-card">
                <div class="card-body p-4 p-lg-5">
                    @if ($errors->has('carteira'))
                        <div class="alert alert-danger" role="alert">
                            {{ $errors->first('carteira') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('usuario.carteirinha.store') }}" class="row g-3">
                        @csrf

                        <div class="col-md-6">
                            <label class="form-label" for="cpf">CPF</label>
                            <input
                                class="form-control @error('cpf') is-invalid @enderror"
                                id="cpf"
                                name="cpf"
                                type="text"
                                value="{{ old('cpf') }}"
                                inputmode="numeric"
                                maxlength="14"
                                placeholder="00000000000"
                                required
                            >
                            @error('cpf')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="telefone">Telefone</label>
                            <input
                                class="form-control @error('telefone') is-invalid @enderror"
                                id="telefone"
                                name="telefone"
                                type="text"
                                value="{{ old('telefone') }}"
                                maxlength="20"
                                placeholder="(11) 99999-9999"
                                required
                            >
                            @error('telefone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="data_nascimento">Data de nascimento</label>
                            <input
                                class="form-control @error('data_nascimento') is-invalid @enderror"
                                id="data_nascimento"
                                name="data_nascimento"
                                type="date"
                                value="{{ old('data_nascimento') }}"
                                max="{{ now()->toDateString() }}"
                                required
                            >
                            @error('data_nascimento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="tipo_sanguineo">Tipo sanguineo</label>
                            <select
                                class="form-select @error('tipo_sanguineo') is-invalid @enderror"
                                id="tipo_sanguineo"
                                name="tipo_sanguineo"
                                required
                            >
                                <option value="">Selecione</option>
                                @foreach ($tiposSanguineos as $tipoSanguineo)
                                    <option value="{{ $tipoSanguineo }}" @selected(old('tipo_sanguineo') === $tipoSanguineo)>
                                        {{ $tipoSanguineo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_sanguineo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="peso">Peso</label>
                            <input
                                class="form-control @error('peso') is-invalid @enderror"
                                id="peso"
                                name="peso"
                                type="number"
                                value="{{ old('peso') }}"
                                min="0.01"
                                max="999.99"
                                step="0.01"
                                placeholder="70.00"
                                required
                            >
                            @error('peso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="cidade">Cidade</label>
                            <input
                                class="form-control @error('cidade') is-invalid @enderror"
                                id="cidade"
                                name="cidade"
                                type="text"
                                value="{{ old('cidade') }}"
                                maxlength="255"
                                required
                            >
                            @error('cidade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-primary" type="submit">Emitir carteirinha</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </section>
</x-layouts.public>

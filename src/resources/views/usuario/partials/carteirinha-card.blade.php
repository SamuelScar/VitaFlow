@php
    $editando = $errors->any();
@endphp

<form
    method="POST"
    action="{{ route('usuario.carteirinha.update') }}"
    class="card border rounded-3 shadow-sm p-4 donor-pass-edit-form donor-pass-card {{ $editando ? 'is-editing' : '' }}"
    x-data="{ editing: @js($editando) }"
    x-bind:class="{ 'is-editing': editing }"
    data-validate-form
>
    @csrf
    @method('PUT')

    <div class="d-flex flex-column flex-lg-row justify-content-between gap-4">
        <div>
            <span class="badge text-bg-light border mb-3">
                <i class="bi bi-person-vcard me-1" aria-hidden="true"></i>
                Carteirinha ativa
            </span>
            <h2 class="h4 fw-bold mb-1">{{ $usuario->name }}</h2>
            <p class="text-secondary mb-0">Doador cadastrado no VitaFlow</p>
        </div>

        <div class="d-flex flex-wrap justify-content-lg-end align-items-start gap-2">
            <button
                class="btn btn-outline-primary"
                type="button"
                x-cloak
                x-show="!editing"
                x-on:click="editing = true; $nextTick(() => $refs.firstPassField?.focus())"
            >
                <i class="bi bi-pencil me-1" aria-hidden="true"></i>
                Editar dados
            </button>
            <button class="btn btn-primary" type="submit" x-cloak x-show="editing">
                <i class="bi bi-check-lg me-1" aria-hidden="true"></i>
                Salvar alteracoes
            </button>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-lg-9">
            <div class="row g-3">
                <div class="col-sm-6">
                    <label class="small text-secondary text-uppercase" for="editar_carteirinha_cpf">CPF</label>
                    <input
                        class="form-control fw-semibold donor-pass-field @error('cpf') is-invalid @enderror"
                        id="editar_carteirinha_cpf"
                        name="cpf"
                        type="text"
                        value="{{ old('cpf', $usuario->cpf) }}"
                        inputmode="numeric"
                        maxlength="14"
                        x-mask="999.999.999-99"
                        required
                        @readonly(! $editando)
                        x-bind:readonly="!editing"
                        x-ref="firstPassField"
                    >
                    @error('cpf')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-sm-6">
                    <label class="small text-secondary text-uppercase" for="editar_carteirinha_telefone">Telefone</label>
                    <input
                        class="form-control fw-semibold donor-pass-field @error('telefone') is-invalid @enderror"
                        id="editar_carteirinha_telefone"
                        name="telefone"
                        type="text"
                        value="{{ old('telefone', $usuario->telefone) }}"
                        inputmode="tel"
                        maxlength="20"
                        x-mask:dynamic="$input.replace(/\D/g, '').length &gt; 10 ? '(99) 99999-9999' : '(99) 9999-9999'"
                        required
                        @readonly(! $editando)
                        x-bind:readonly="!editing"
                    >
                    @error('telefone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-sm-6">
                    <label class="small text-secondary text-uppercase" for="editar_carteirinha_cidade">Cidade</label>
                    <input
                        class="form-control fw-semibold donor-pass-field @error('cidade') is-invalid @enderror"
                        id="editar_carteirinha_cidade"
                        name="cidade"
                        type="text"
                        value="{{ old('cidade', $usuario->cidade) }}"
                        maxlength="255"
                        required
                        @readonly(! $editando)
                        x-bind:readonly="!editing"
                    >
                    @error('cidade')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-sm-6">
                    <label class="small text-secondary text-uppercase" for="editar_carteirinha_data_nascimento">Nascimento</label>
                    <input
                        class="form-control fw-semibold donor-pass-field @error('data_nascimento') is-invalid @enderror"
                        id="editar_carteirinha_data_nascimento"
                        name="data_nascimento"
                        type="date"
                        value="{{ old('data_nascimento', $usuario->data_nascimento->format('Y-m-d')) }}"
                        max="{{ now()->toDateString() }}"
                        required
                        @readonly(! $editando)
                        x-bind:readonly="!editing"
                    >
                    @error('data_nascimento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-sm-6">
                    <label class="small text-secondary text-uppercase" for="editar_carteirinha_peso">Peso</label>
                    <input
                        class="form-control fw-semibold donor-pass-field @error('peso') is-invalid @enderror"
                        id="editar_carteirinha_peso"
                        name="peso"
                        type="number"
                        value="{{ old('peso', $usuario->peso) }}"
                        min="0.01"
                        max="999.99"
                        step="0.01"
                        required
                        @readonly(! $editando)
                        x-bind:readonly="!editing"
                    >
                    @error('peso')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="border rounded-3 p-3 text-center h-100">
                <label class="small text-secondary text-uppercase d-block" for="editar_carteirinha_tipo_sanguineo">
                    Tipo sanguineo
                </label>
                <select
                    class="form-select fs-2 fw-bold lh-1 mt-2 text-center text-primary donor-pass-field @error('tipo_sanguineo') is-invalid @enderror"
                    id="editar_carteirinha_tipo_sanguineo"
                    name="tipo_sanguineo"
                    required
                    @disabled(! $editando)
                    x-bind:disabled="!editing"
                >
                    @foreach ($tiposSanguineos as $tipoSanguineo)
                        <option value="{{ $tipoSanguineo }}" @selected(old('tipo_sanguineo', $usuario->tipo_sanguineo) === $tipoSanguineo)>
                            {{ $tipoSanguineo }}
                        </option>
                    @endforeach
                </select>
                @error('tipo_sanguineo')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="border-top d-flex flex-wrap gap-4 mt-4 pt-4">
        <div>
            <span class="small text-secondary text-uppercase d-block">Status</span>
            <span class="badge text-bg-success text-uppercase">
                <i class="bi bi-check-circle me-1" aria-hidden="true"></i>
                {{ $carteira->status }}
            </span>
        </div>
        <div>
            <span class="small text-secondary text-uppercase d-block">Emitida em</span>
            <strong>{{ $carteira->emitida_em->format('d/m/Y') }}</strong>
        </div>
        <p class="text-secondary mb-0">A carteirinha ativa autoriza o usuario a realizar agendamentos de doacao de sangue.</p>
    </div>
</form>

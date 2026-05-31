<form method="POST" action="{{ $action }}" class="row g-3" data-validate-form>
    @csrf

    @isset($method)
        @method($method)
    @endisset

    <div class="col-md-6">
        <label class="form-label" for="{{ $idPrefix }}_cpf">CPF</label>
        <input
            class="form-control @error('cpf') is-invalid @enderror"
            id="{{ $idPrefix }}_cpf"
            name="cpf"
            type="text"
            value="{{ old('cpf', $carteira?->cpf) }}"
            inputmode="numeric"
            maxlength="14"
            placeholder="000.000.000-00"
            x-mask="999.999.999-99"
            required
        >
        @error('cpf')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="{{ $idPrefix }}_telefone">Telefone</label>
        <input
            class="form-control @error('telefone') is-invalid @enderror"
            id="{{ $idPrefix }}_telefone"
            name="telefone"
            type="text"
            value="{{ old('telefone', $carteira?->telefone) }}"
            inputmode="tel"
            maxlength="20"
            placeholder="(11) 99999-9999"
            x-mask:dynamic="$input.replace(/\D/g, '').length &gt; 10 ? '(99) 99999-9999' : '(99) 9999-9999'"
            required
        >
        @error('telefone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="{{ $idPrefix }}_data_nascimento">Data de nascimento</label>
        <input
            class="form-control @error('data_nascimento') is-invalid @enderror"
            id="{{ $idPrefix }}_data_nascimento"
            name="data_nascimento"
            type="date"
            value="{{ old('data_nascimento', $carteira?->data_nascimento?->format('Y-m-d')) }}"
            max="{{ now()->toDateString() }}"
            required
        >
        @error('data_nascimento')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="{{ $idPrefix }}_tipo_sanguineo">Tipo sanguineo</label>
        <select
            class="form-select @error('tipo_sanguineo') is-invalid @enderror"
            id="{{ $idPrefix }}_tipo_sanguineo"
            name="tipo_sanguineo"
            required
        >
            <option value="">Selecione</option>
            @foreach ($tiposSanguineos as $tipoSanguineo)
                <option value="{{ $tipoSanguineo }}" @selected(old('tipo_sanguineo', $carteira?->tipo_sanguineo) === $tipoSanguineo)>
                    {{ $tipoSanguineo }}
                </option>
            @endforeach
        </select>
        @error('tipo_sanguineo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="{{ $idPrefix }}_peso">Peso</label>
        <input
            class="form-control @error('peso') is-invalid @enderror"
            id="{{ $idPrefix }}_peso"
            name="peso"
            type="number"
            value="{{ old('peso', $carteira?->peso) }}"
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
        <label class="form-label" for="{{ $idPrefix }}_cidade">Cidade</label>
        <input
            class="form-control @error('cidade') is-invalid @enderror"
            id="{{ $idPrefix }}_cidade"
            name="cidade"
            type="text"
            value="{{ old('cidade', $carteira?->cidade) }}"
            maxlength="255"
            required
        >
        @error('cidade')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 d-flex justify-content-end">
        <button class="btn btn-primary d-inline-flex align-items-center gap-2" type="submit">
            <i class="bi bi-check-lg" aria-hidden="true"></i>
            {{ $submitLabel }}
        </button>
    </div>
</form>

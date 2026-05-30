@php
    $useOldValues = $useOldValues ?? false;
    $fieldValue = fn (string $field, mixed $default = '') => $useOldValues ? old($field, $default) : $default;
@endphp

<form method="POST" action="{{ $action }}" class="row g-3" data-validate-form data-cep-lookup>
    @csrf

    @isset($method)
        @method($method)
    @endisset

    @if ($localColeta?->exists)
        <input type="hidden" name="local_coleta_id" value="{{ $localColeta->id }}">
    @endif

    <div class="col-md-8">
        <label class="form-label" for="{{ $idPrefix }}_nome">Nome</label>
        <input
            class="form-control @error('nome', $errorBag) is-invalid @enderror"
            id="{{ $idPrefix }}_nome"
            name="nome"
            type="text"
            value="{{ $fieldValue('nome', $localColeta?->nome) }}"
            maxlength="255"
            required
        >
        @error('nome', $errorBag)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="{{ $idPrefix }}_capacidade_diaria">Capacidade diaria</label>
        <input
            class="form-control @error('capacidade_diaria', $errorBag) is-invalid @enderror"
            id="{{ $idPrefix }}_capacidade_diaria"
            name="capacidade_diaria"
            type="number"
            value="{{ $fieldValue('capacidade_diaria', $localColeta?->capacidade_diaria) }}"
            min="1"
            max="10000"
            step="1"
            required
        >
        @error('capacidade_diaria', $errorBag)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="{{ $idPrefix }}_cep">CEP</label>
        <input
            class="form-control @error('cep', $errorBag) is-invalid @enderror"
            id="{{ $idPrefix }}_cep"
            name="cep"
            type="text"
            value="{{ $fieldValue('cep', $localColeta?->cep) }}"
            inputmode="numeric"
            maxlength="9"
            pattern="\d{5}-?\d{3}"
            data-cep-field="cep"
            required
        >
        @error('cep', $errorBag)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="{{ $idPrefix }}_logradouro">Logradouro</label>
        <input
            class="form-control @error('logradouro', $errorBag) is-invalid @enderror"
            id="{{ $idPrefix }}_logradouro"
            name="logradouro"
            type="text"
            value="{{ $fieldValue('logradouro', $localColeta?->logradouro) }}"
            maxlength="255"
            data-cep-field="logradouro"
            required
        >
        @error('logradouro', $errorBag)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="{{ $idPrefix }}_numero">Numero</label>
        <input
            class="form-control @error('numero', $errorBag) is-invalid @enderror"
            id="{{ $idPrefix }}_numero"
            name="numero"
            type="text"
            value="{{ $fieldValue('numero', $localColeta?->numero) }}"
            maxlength="30"
            data-cep-field="numero"
            required
        >
        @error('numero', $errorBag)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="{{ $idPrefix }}_bairro">Bairro</label>
        <input
            class="form-control @error('bairro', $errorBag) is-invalid @enderror"
            id="{{ $idPrefix }}_bairro"
            name="bairro"
            type="text"
            value="{{ $fieldValue('bairro', $localColeta?->bairro) }}"
            maxlength="255"
            data-cep-field="bairro"
            required
        >
        @error('bairro', $errorBag)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-5">
        <label class="form-label" for="{{ $idPrefix }}_cidade">Cidade</label>
        <input
            class="form-control @error('cidade', $errorBag) is-invalid @enderror"
            id="{{ $idPrefix }}_cidade"
            name="cidade"
            type="text"
            value="{{ $fieldValue('cidade', $localColeta?->cidade) }}"
            maxlength="255"
            data-cep-field="cidade"
            required
        >
        @error('cidade', $errorBag)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="{{ $idPrefix }}_uf">UF</label>
        <input
            class="form-control text-uppercase @error('uf', $errorBag) is-invalid @enderror"
            id="{{ $idPrefix }}_uf"
            name="uf"
            type="text"
            value="{{ $fieldValue('uf', $localColeta?->uf) }}"
            maxlength="2"
            data-cep-field="uf"
            required
        >
        @error('uf', $errorBag)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label" for="{{ $idPrefix }}_complemento">Complemento</label>
        <input
            class="form-control @error('complemento', $errorBag) is-invalid @enderror"
            id="{{ $idPrefix }}_complemento"
            name="complemento"
            type="text"
            value="{{ $fieldValue('complemento', $localColeta?->complemento) }}"
            maxlength="255"
        >
        @error('complemento', $errorBag)
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

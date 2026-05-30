@php
    $useOldValues = $useOldValues ?? false;
    $fieldValue = fn (string $field, mixed $default = '') => $useOldValues ? old($field, $default) : $default;
@endphp

<form method="POST" action="{{ $action }}" class="row g-3" data-validate-form>
    @csrf

    @isset($method)
        @method($method)
    @endisset

    @if ($localColeta?->exists)
        <input type="hidden" name="local_coleta_id" value="{{ $localColeta->id }}">
    @endif

    <div class="col-md-6">
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

    <div class="col-md-6">
        <label class="form-label" for="{{ $idPrefix }}_cidade">Cidade</label>
        <input
            class="form-control @error('cidade', $errorBag) is-invalid @enderror"
            id="{{ $idPrefix }}_cidade"
            name="cidade"
            type="text"
            value="{{ $fieldValue('cidade', $localColeta?->cidade) }}"
            maxlength="255"
            required
        >
        @error('cidade', $errorBag)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-8">
        <label class="form-label" for="{{ $idPrefix }}_endereco">Endereco</label>
        <input
            class="form-control @error('endereco', $errorBag) is-invalid @enderror"
            id="{{ $idPrefix }}_endereco"
            name="endereco"
            type="text"
            value="{{ $fieldValue('endereco', $localColeta?->endereco) }}"
            maxlength="255"
            required
        >
        @error('endereco', $errorBag)
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

    <div class="col-12 d-flex justify-content-end">
        <button class="btn btn-primary d-inline-flex align-items-center gap-2" type="submit">
            <i class="bi bi-check-lg" aria-hidden="true"></i>
            {{ $submitLabel }}
        </button>
    </div>
</form>

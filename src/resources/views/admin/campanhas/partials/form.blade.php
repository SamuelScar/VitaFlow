@php
    $campanha = $campanha ?? null;
    $useOldValues = $useOldValues ?? false;
    $fieldValue = fn (string $field, mixed $default = '') => $useOldValues ? old($field, $default) : $default;
    $oldTiposSanguineos = old('tipos_sanguineos_alvo_preenchido') !== null;
    $tiposSelecionados = $useOldValues && $oldTiposSanguineos
        ? old('tipos_sanguineos_alvo', [])
        : ($campanha?->tipos_sanguineos_alvo ?? []);
    $tiposSelecionados = is_array($tiposSelecionados) ? $tiposSelecionados : [];
    $fieldErrors = $errors->getBag($errorBag);
@endphp

<form method="POST" action="{{ $action }}" class="row g-3" data-validate-form>
    @csrf
    <input type="hidden" name="tipos_sanguineos_alvo_preenchido" value="1">

    @isset($method)
        @method($method)
    @endisset

    @if ($campanha?->exists)
        <input type="hidden" name="campanha_id" value="{{ $campanha->id }}">
    @endif

    <div class="col-md-6">
        <label class="form-label" for="{{ $idPrefix }}_titulo">Titulo</label>
        <input
            class="form-control @error('titulo', $errorBag) is-invalid @enderror"
            id="{{ $idPrefix }}_titulo"
            name="titulo"
            type="text"
            value="{{ $fieldValue('titulo', $campanha?->titulo) }}"
            maxlength="255"
            required
        >
        @error('titulo', $errorBag)
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <div class="invalid-feedback">Informe o titulo da campanha.</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="{{ $idPrefix }}_local_coleta_id">Local de coleta</label>
        <select
            class="form-select @error('local_coleta_id', $errorBag) is-invalid @enderror"
            id="{{ $idPrefix }}_local_coleta_id"
            name="local_coleta_id"
            required
        >
            <option value="">Selecione</option>
            @foreach ($locaisColeta as $localColeta)
                <option
                    value="{{ $localColeta->id }}"
                    @selected((string) $fieldValue('local_coleta_id', $campanha?->local_coleta_id) === (string) $localColeta->id)
                >
                    {{ $localColeta->nome }}
                </option>
            @endforeach
        </select>
        @error('local_coleta_id', $errorBag)
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <div class="invalid-feedback">Selecione um local de coleta.</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label" for="{{ $idPrefix }}_descricao">Descricao</label>
        <textarea
            class="form-control @error('descricao', $errorBag) is-invalid @enderror"
            id="{{ $idPrefix }}_descricao"
            name="descricao"
            rows="3"
            maxlength="5000"
            required
        >{{ $fieldValue('descricao', $campanha?->descricao) }}</textarea>
        @error('descricao', $errorBag)
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <div class="invalid-feedback">Informe a descricao da campanha.</div>
        @enderror
    </div>

    <div class="col-md-6">
        <fieldset>
            <legend class="form-label fs-6 mb-2">
                <i class="bi bi-droplet-half me-1" aria-hidden="true"></i>
                Tipos alvo
            </legend>
            <div class="d-flex flex-wrap gap-2">
                @foreach ($tiposSanguineos as $tipoSanguineo)
                    <input
                        class="btn-check @if ($fieldErrors->has('tipos_sanguineos_alvo') || $fieldErrors->has('tipos_sanguineos_alvo.*')) is-invalid @endif"
                        id="{{ $idPrefix }}_tipo_sanguineo_{{ str_replace(['+', '-'], ['positivo', 'negativo'], $tipoSanguineo) }}"
                        name="tipos_sanguineos_alvo[]"
                        type="checkbox"
                        value="{{ $tipoSanguineo }}"
                        autocomplete="off"
                        @checked(in_array($tipoSanguineo, $tiposSelecionados, true))
                    >
                    <label
                        class="btn btn-outline-secondary"
                        for="{{ $idPrefix }}_tipo_sanguineo_{{ str_replace(['+', '-'], ['positivo', 'negativo'], $tipoSanguineo) }}"
                    >
                        {{ $tipoSanguineo }}
                    </label>
                @endforeach
            </div>
            <div class="form-text">Nenhum tipo selecionado aplica a campanha para todos.</div>

            @if ($fieldErrors->has('tipos_sanguineos_alvo') || $fieldErrors->has('tipos_sanguineos_alvo.*'))
                <div class="invalid-feedback d-block">
                    {{ $fieldErrors->first('tipos_sanguineos_alvo') ?: $fieldErrors->first('tipos_sanguineos_alvo.*') }}
                </div>
            @endif
        </fieldset>
    </div>

    <div class="col-md-2">
        <label class="form-label" for="{{ $idPrefix }}_meta_bolsas">Meta de bolsas</label>
        <input
            class="form-control @error('meta_bolsas', $errorBag) is-invalid @enderror"
            id="{{ $idPrefix }}_meta_bolsas"
            name="meta_bolsas"
            type="number"
            value="{{ $fieldValue('meta_bolsas', $campanha?->meta_bolsas) }}"
            min="1"
            max="100000"
            step="1"
            required
        >
        @error('meta_bolsas', $errorBag)
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <div class="invalid-feedback">Informe uma meta de bolsas valida.</div>
        @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label" for="{{ $idPrefix }}_data_inicio">Inicio</label>
        <input
            class="form-control @error('data_inicio', $errorBag) is-invalid @enderror"
            id="{{ $idPrefix }}_data_inicio"
            name="data_inicio"
            type="date"
            value="{{ $fieldValue('data_inicio', $campanha?->data_inicio?->format('Y-m-d')) }}"
            @unless ($campanha?->exists)
                min="{{ now()->toDateString() }}"
            @endunless
            required
        >
        @error('data_inicio', $errorBag)
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <div class="invalid-feedback">Informe uma data de inicio valida.</div>
        @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label" for="{{ $idPrefix }}_data_fim">Fim</label>
        <input
            class="form-control @error('data_fim', $errorBag) is-invalid @enderror"
            id="{{ $idPrefix }}_data_fim"
            name="data_fim"
            type="date"
            value="{{ $fieldValue('data_fim', $campanha?->data_fim?->format('Y-m-d')) }}"
            data-after-or-equal-to="[name='data_inicio']"
            data-after-or-equal-message="A data fim deve ser posterior ou igual a data inicio."
            required
        >
        @error('data_fim', $errorBag)
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <div class="invalid-feedback">A data fim deve ser posterior ou igual a data inicio.</div>
        @enderror
    </div>

    @if ($campanha?->exists)
        <div class="col-md-4">
            <label class="form-label" for="{{ $idPrefix }}_status">Status</label>
            <select
                class="form-select @error('status', $errorBag) is-invalid @enderror"
                id="{{ $idPrefix }}_status"
                name="status"
                required
            >
                <option value="ativa" @selected($fieldValue('status', $campanha->status) === 'ativa')>Ativa</option>
                <option value="encerrada" @selected($fieldValue('status', $campanha->status) === 'encerrada')>Encerrada</option>
                <option value="cancelada" @selected($fieldValue('status', $campanha->status) === 'cancelada')>Cancelada</option>
            </select>
            @error('status', $errorBag)
                <div class="invalid-feedback">{{ $message }}</div>
            @else
                <div class="invalid-feedback">Selecione um status.</div>
            @enderror
        </div>
    @endif

    <div class="col-12 d-flex justify-content-end">
        <button class="btn btn-primary d-inline-flex align-items-center gap-2" type="submit" @disabled($locaisColeta->isEmpty())>
            <i class="bi bi-check-lg" aria-hidden="true"></i>
            {{ $submitLabel }}
        </button>
    </div>
</form>

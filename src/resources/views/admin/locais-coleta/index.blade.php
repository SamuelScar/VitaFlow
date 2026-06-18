<x-layouts.public title="Locais de coleta">
    @php
        $criando = $errors->storeLocalColeta->any();
    @endphp

    <x-page-header
        label="Administracao"
        title="Locais de coleta"
        description="Cadastre e mantenha os pontos onde as doacoes de sangue acontecem."
        icon="bi-shield-check"
        :back-href="route('admin.dashboard')"
    />

    <section class="container py-5">
        <article class="card shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                    <div>
                        <h2 class="h5 fw-bold mb-1">Locais cadastrados</h2>
                        <p class="text-secondary mb-0">Gerencie os locais disponiveis para campanhas.</p>
                    </div>

                    <div class="d-flex flex-wrap align-items-start gap-2">
                        <span class="badge text-bg-light border">
                            <i class="bi bi-geo-alt me-1" aria-hidden="true"></i>
                            {{ $locaisColeta->count() }} {{ $locaisColeta->count() === 1 ? 'local' : 'locais' }}
                        </span>
                        <button
                            class="btn btn-primary"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#novo-local-coleta"
                            aria-expanded="{{ $criando ? 'true' : 'false' }}"
                            aria-controls="novo-local-coleta"
                        >
                            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>
                            Novo local
                        </button>
                    </div>
                </div>

                <div class="collapse {{ $criando ? 'show' : '' }} mb-4" id="novo-local-coleta">
                    <div class="border rounded-3 p-3">
                        <h3 class="h6 fw-bold mb-1">Cadastrar local</h3>
                        <p class="text-secondary mb-3">Informe os dados do novo ponto de coleta.</p>

                        @include('admin.locais-coleta.partials.form', [
                            'action' => route('admin.locais-coleta.store'),
                            'submitLabel' => 'Cadastrar local',
                            'idPrefix' => 'criar_local_coleta',
                            'localColeta' => null,
                            'errorBag' => 'storeLocalColeta',
                            'useOldValues' => $criando,
                        ])
                    </div>
                </div>

                @forelse ($locaisColeta as $localColeta)
                    @php
                        $editando = $errors->updateLocalColeta->any()
                            && (int) old('local_coleta_id') === $localColeta->id;
                    @endphp

                    <div class="border rounded-3 p-3 mb-3">
                        <div class="d-flex flex-column flex-xl-row justify-content-between gap-3">
                            <div class="flex-grow-1">
                                <h3 class="h6 fw-bold mb-1">{{ $localColeta->nome }}</h3>
                                <p class="text-secondary mb-2">{{ $localColeta->endereco_completo }}</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge text-bg-light border">
                                        <i class="bi bi-building me-1" aria-hidden="true"></i>
                                        {{ $localColeta->cidade }}{{ $localColeta->uf ? "/{$localColeta->uf}" : '' }}
                                    </span>
                                    @if ($localColeta->bairro)
                                        <span class="badge text-bg-light border">
                                            <i class="bi bi-signpost me-1" aria-hidden="true"></i>
                                            {{ $localColeta->bairro }}
                                        </span>
                                    @endif
                                    <span class="badge text-bg-light border">
                                        <i class="bi bi-mailbox me-1" aria-hidden="true"></i>
                                        {{ $localColeta->cep ?? 'CEP nao informado' }}
                                    </span>
                                    <span class="badge text-bg-light border">
                                        <i class="bi bi-calendar2-day me-1" aria-hidden="true"></i>
                                        {{ $localColeta->capacidade_diaria }} doacoes/dia
                                    </span>
                                </div>
                            </div>

                            <div class="d-grid d-sm-flex flex-sm-nowrap flex-shrink-0 align-items-start justify-content-sm-end gap-2">
                                <button
                                    class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-2"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#editar-local-{{ $localColeta->id }}"
                                    aria-expanded="{{ $editando ? 'true' : 'false' }}"
                                    aria-controls="editar-local-{{ $localColeta->id }}"
                                >
                                    <i class="bi bi-pencil" aria-hidden="true"></i>
                                    Editar
                                </button>

                                <form
                                    class="d-grid m-0"
                                    method="POST"
                                    action="{{ route('admin.locais-coleta.destroy', $localColeta) }}"
                                    data-confirm-title="Excluir local de coleta?"
                                    data-confirm-text="Esta acao nao podera ser desfeita."
                                    data-confirm-button-text="Excluir"
                                    data-confirm-button-color="#c62828"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger d-inline-flex align-items-center justify-content-center gap-2" type="submit">
                                        <i class="bi bi-trash" aria-hidden="true"></i>
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="collapse {{ $editando ? 'show' : '' }} mt-4" id="editar-local-{{ $localColeta->id }}">
                            @include('admin.locais-coleta.partials.form', [
                                'action' => route('admin.locais-coleta.update', $localColeta),
                                'method' => 'PUT',
                                'submitLabel' => 'Salvar alteracoes',
                                'idPrefix' => "editar_local_coleta_{$localColeta->id}",
                                'localColeta' => $localColeta,
                                'errorBag' => 'updateLocalColeta',
                                'useOldValues' => $editando,
                            ])
                        </div>
                    </div>
                @empty
                    <div class="border rounded-3 p-4 text-center">
                        <h3 class="h6 fw-bold mb-1">Nenhum local cadastrado</h3>
                        <p class="text-secondary mb-0">Cadastre o primeiro local para usar nas campanhas.</p>
                    </div>
                @endforelse
            </div>
        </article>
    </section>
</x-layouts.public>

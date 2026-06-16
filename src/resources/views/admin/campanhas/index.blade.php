<x-layouts.public title="Campanhas">
    @php
        $criando = $errors->storeCampanha->any();
        $statusLabels = [
            'ativa' => 'Ativa',
            'encerrada' => 'Encerrada',
            'cancelada' => 'Cancelada',
        ];
        $statusClasses = [
            'ativa' => 'text-bg-success',
            'encerrada' => 'text-bg-secondary',
            'cancelada' => 'text-bg-danger',
        ];
    @endphp

    <section class="bg-white border-bottom">
        <div class="container py-5">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <span class="badge text-bg-light border mb-3">
                        <i class="bi bi-shield-check me-1" aria-hidden="true"></i>
                        Administracao
                    </span>
                    <h1 class="h2 fw-bold mb-2">Campanhas de sangue</h1>
                    <p class="text-secondary mb-0">
                        Cadastre e mantenha campanhas vinculadas aos locais de coleta.
                    </p>
                </div>

                <a class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    Voltar
                </a>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <article class="card shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                    <div>
                        <h2 class="h5 fw-bold mb-1">Campanhas cadastradas</h2>
                        <p class="text-secondary mb-0">Gerencie as campanhas disponiveis para doacao.</p>
                    </div>

                    <div class="d-flex flex-wrap align-items-start gap-2">
                        <span class="badge text-bg-light border">
                            <i class="bi bi-megaphone me-1" aria-hidden="true"></i>
                            {{ $totalCampanhas }} {{ $totalCampanhas === 1 ? 'campanha' : 'campanhas' }}
                        </span>
                        <button
                            class="btn btn-primary"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#nova-campanha"
                            aria-expanded="{{ $criando ? 'true' : 'false' }}"
                            aria-controls="nova-campanha"
                        >
                            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>
                            Nova campanha
                        </button>
                    </div>
                </div>

                @if ($locaisColeta->isEmpty())
                    <div class="border border-warning-subtle bg-warning-subtle rounded-3 p-3 mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle text-warning-emphasis" aria-hidden="true"></i>
                            <span class="text-warning-emphasis">Cadastre um local de coleta antes de criar campanhas.</span>
                        </div>
                    </div>
                @endif

                <div class="collapse {{ $criando ? 'show' : '' }} mb-4" id="nova-campanha">
                    <div class="border rounded-3 p-3">
                        <h3 class="h6 fw-bold mb-1">Cadastrar campanha</h3>
                        <p class="text-secondary mb-3">Informe os dados da nova campanha de doacao de sangue.</p>

                        @include('admin.campanhas.partials.form', [
                            'action' => route('admin.campanhas.store'),
                            'submitLabel' => 'Cadastrar campanha',
                            'idPrefix' => 'criar_campanha',
                            'campanha' => null,
                            'locaisColeta' => $locaisColeta,
                            'tiposSanguineos' => $tiposSanguineos,
                            'errorBag' => 'storeCampanha',
                            'useOldValues' => $criando,
                        ])
                    </div>
                </div>

                @forelse ($campanhas as $campanha)
                    @php
                        $editando = $errors->updateCampanha->any()
                            && (int) old('campanha_id') === $campanha->id;
                        $tiposAlvo = $campanha->tipos_sanguineos_alvo;
                        $tiposAlvo = is_array($tiposAlvo) && count($tiposAlvo) > 0
                            ? implode(', ', $tiposAlvo)
                            : 'Todos';
                    @endphp

                    <div class="border rounded-3 p-3 mb-3">
                        <div class="d-flex flex-column flex-xl-row justify-content-between gap-3">
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                    <h3 class="h6 fw-bold mb-0">{{ $campanha->titulo }}</h3>
                                    <span class="badge {{ $statusClasses[$campanha->status] ?? 'text-bg-light' }}">
                                        <i class="bi bi-circle-fill me-1" aria-hidden="true"></i>
                                        {{ $statusLabels[$campanha->status] ?? $campanha->status }}
                                    </span>
                                </div>

                                <p class="text-secondary mb-2">{{ $campanha->descricao }}</p>

                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge text-bg-light border">
                                        <i class="bi bi-geo-alt me-1" aria-hidden="true"></i>
                                        {{ $campanha->localColeta?->nome ?? 'Local removido' }}
                                    </span>
                                    <span class="badge text-bg-light border">
                                        <i class="bi bi-droplet-half me-1" aria-hidden="true"></i>
                                        Tipos alvo: {{ $tiposAlvo }}
                                    </span>
                                    <span class="badge text-bg-light border">
                                        <i class="bi bi-bullseye me-1" aria-hidden="true"></i>
                                        {{ $campanha->meta_bolsas }} bolsas
                                    </span>
                                    <span class="badge text-bg-light border">
                                        <i class="bi bi-people me-1" aria-hidden="true"></i>
                                        {{ $campanha->agendamentos_por_horario }} por horario
                                    </span>
                                    <span class="badge text-bg-light border">
                                        <i class="bi bi-clock me-1" aria-hidden="true"></i>
                                        {{ substr((string) $campanha->horario_inicio, 0, 5) }} as {{ substr((string) $campanha->horario_fim, 0, 5) }}
                                    </span>
                                    <span class="badge text-bg-light border">
                                        <i class="bi bi-calendar-event me-1" aria-hidden="true"></i>
                                        {{ $campanha->data_inicio->format('d/m/Y') }} ate {{ $campanha->data_fim->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>

                            <div class="d-grid d-sm-flex flex-sm-nowrap flex-shrink-0 align-items-start justify-content-sm-end gap-2">
                                <button
                                    class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-2"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#editar-campanha-{{ $campanha->id }}"
                                    aria-expanded="{{ $editando ? 'true' : 'false' }}"
                                    aria-controls="editar-campanha-{{ $campanha->id }}"
                                >
                                    <i class="bi bi-pencil" aria-hidden="true"></i>
                                    Editar
                                </button>

                                <form
                                    class="d-grid m-0"
                                    method="POST"
                                    action="{{ route('admin.campanhas.destroy', $campanha) }}"
                                    data-confirm-title="Excluir campanha?"
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

                        <div class="collapse {{ $editando ? 'show' : '' }} mt-4" id="editar-campanha-{{ $campanha->id }}">
                            @include('admin.campanhas.partials.form', [
                                'action' => route('admin.campanhas.update', $campanha),
                                'method' => 'PUT',
                                'submitLabel' => 'Salvar alteracoes',
                                'idPrefix' => "editar_campanha_{$campanha->id}",
                                'campanha' => $campanha,
                                'locaisColeta' => $locaisColeta,
                                'tiposSanguineos' => $tiposSanguineos,
                                'errorBag' => 'updateCampanha',
                                'useOldValues' => $editando,
                            ])
                        </div>
                    </div>
                @empty
                    <div class="border rounded-3 p-4 text-center">
                        <h3 class="h6 fw-bold mb-1">Nenhuma campanha cadastrada</h3>
                        <p class="text-secondary mb-0">Cadastre a primeira campanha para iniciar o fluxo de doacao.</p>
                    </div>
                @endforelse

                @if ($campanhas->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $campanhas->links() }}
                    </div>
                @endif
            </div>
        </article>
    </section>
</x-layouts.public>

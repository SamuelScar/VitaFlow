<div>
    @php
        $statusLabels = [
            'agendado' => 'Agendado',
            'cancelado' => 'Cancelado',
            'realizado' => 'Realizado',
            'faltou' => 'Faltou',
        ];
        $statusClasses = [
            'agendado' => 'text-bg-primary',
            'cancelado' => 'text-bg-secondary',
            'realizado' => 'text-bg-success',
            'faltou' => 'text-bg-warning',
        ];
        $totalFiltrado = $agendamentos->total();
    @endphp

    <article class="card shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                <div>
                    <h2 class="h5 fw-bold mb-1">Filtros</h2>
                    <p class="text-secondary mb-0">Refine a lista para acompanhar uma campanha ou periodo especifico.</p>
                </div>

                <span class="badge text-bg-light border align-self-start">
                    <i class="bi bi-calendar-check me-1" aria-hidden="true"></i>
                    {{ $totalFiltrado }} {{ $totalFiltrado === 1 ? 'agendamento' : 'agendamentos' }}
                </span>
            </div>

            <div class="row g-3">
                <div class="col-12 col-lg-4">
                    <label class="form-label fw-semibold" for="campanha_id">Campanha</label>
                    <select class="form-select" id="campanha_id" wire:model.live="campanhaId">
                        <option value="">Todas as campanhas</option>
                        @foreach ($campanhas as $campanha)
                            <option value="{{ $campanha->id }}">{{ $campanha->titulo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-lg-4">
                    <label class="form-label fw-semibold" for="local_coleta_id">Local</label>
                    <select class="form-select" id="local_coleta_id" wire:model.live="localColetaId">
                        <option value="">Todos os locais</option>
                        @foreach ($locaisColeta as $local)
                            <option value="{{ $local->id }}">{{ $local->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-lg-4">
                    <label class="form-label fw-semibold" for="status">Status</label>
                    <select class="form-select" id="status" wire:model.live="status">
                        <option value="">Todos os status</option>
                        @foreach ($statusOptions as $status)
                            <option value="{{ $status }}">{{ $statusLabels[$status] ?? ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label fw-semibold" for="data_inicio">De</label>
                    <input class="form-control" id="data_inicio" type="date" wire:model.live="dataInicio">
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label fw-semibold" for="data_fim">Ate</label>
                    <input class="form-control" id="data_fim" type="date" wire:model.live="dataFim">
                </div>

                <div class="col-12 col-lg-6 d-flex flex-column flex-sm-row align-items-stretch align-items-sm-end justify-content-lg-end gap-2">
                    <button
                        class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-2"
                        type="button"
                        wire:click="limparFiltros"
                        wire:loading.attr="disabled"
                        @disabled(! $filtrosAtivos)
                    >
                        <i class="bi bi-x-lg" aria-hidden="true"></i>
                        Limpar filtros
                    </button>
                </div>
            </div>
        </div>
    </article>

    <div class="row g-3 mb-4">
        @foreach ($statusOptions as $status)
            @php
                $totalStatus = (int) ($resumoStatus[$status] ?? 0);
            @endphp
            <div class="col-6 col-lg-3">
                <div class="border rounded-3 p-3 h-100">
                    <span class="badge {{ $statusClasses[$status] ?? 'text-bg-light' }} mb-2">
                        {{ $statusLabels[$status] ?? ucfirst($status) }}
                    </span>
                    <strong class="fs-4 d-block">{{ $totalStatus }}</strong>
                    <span class="text-secondary small">{{ $totalStatus === 1 ? 'registro filtrado' : 'registros filtrados' }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <article class="card shadow-sm rounded-3">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                <div>
                    <h2 class="h5 fw-bold mb-1">Lista de agendamentos</h2>
                    <p class="text-secondary mb-0">
                        @if ($agendamentos->total() > 0)
                            Exibindo {{ $agendamentos->firstItem() }} a {{ $agendamentos->lastItem() }} de {{ $agendamentos->total() }} registros.
                        @else
                            Nenhum registro encontrado para os filtros atuais.
                        @endif
                    </p>
                </div>

                <div class="align-self-lg-start">
                    <label class="form-label fw-semibold" for="agendamentos_por_pagina">Por pagina</label>
                    <select class="form-select" id="agendamentos_por_pagina" wire:model.live="porPagina">
                        @foreach ($opcoesPorPagina as $opcao)
                            <option value="{{ $opcao }}">{{ $opcao }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div wire:loading.class="opacity-50" wire:target="campanhaId,localColetaId,status,dataInicio,dataFim,porPagina,limparFiltros,previousPage,nextPage,gotoPage">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Data</th>
                                <th scope="col">Doador</th>
                                <th scope="col">Campanha</th>
                                <th scope="col">Local</th>
                                <th scope="col">Status</th>
                                <th scope="col">Doacao</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($agendamentos as $agendamento)
                                <tr wire:key="agendamento-{{ $agendamento->id }}">
                                    <td>
                                        <strong>{{ $agendamento->data_hora->format('d/m/Y') }}</strong>
                                        <span class="d-block text-secondary small">{{ $agendamento->data_hora->format('H:i') }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $agendamento->user?->name ?? 'Doador removido' }}</strong>
                                        <span class="d-block text-secondary small">{{ $agendamento->user?->email ?? 'E-mail indisponivel' }}</span>
                                    </td>
                                    <td>{{ $agendamento->campanha?->titulo ?? 'Campanha indisponivel' }}</td>
                                    <td>{{ $agendamento->campanha?->localColeta?->nome ?? 'Local indisponivel' }}</td>
                                    <td>
                                        <span class="badge {{ $statusClasses[$agendamento->status] ?? 'text-bg-light' }}">
                                            {{ $statusLabels[$agendamento->status] ?? ucfirst($agendamento->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($agendamento->doacao)
                                            <span class="badge {{ $agendamento->doacao->status === 'confirmada' ? 'text-bg-success' : 'text-bg-secondary' }}">
                                                {{ $agendamento->doacao->status === 'confirmada' ? 'Confirmada' : 'Recusada' }}
                                            </span>
                                        @else
                                            <span class="badge text-bg-light border">Sem registro</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center text-secondary py-4" colspan="6">
                                        Nenhum agendamento encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($agendamentos->hasPages())
                <div class="mt-4 agendamentos-pagination">
                    {{ $agendamentos->links() }}
                </div>
            @endif
        </div>
    </article>
</div>

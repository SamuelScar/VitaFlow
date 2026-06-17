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
        $doacaoStatusLabels = [
            'confirmada' => 'Confirmada',
            'recusada' => 'Recusada',
        ];
        $doacaoStatusClasses = [
            'confirmada' => 'text-bg-success',
            'recusada' => 'text-bg-secondary',
        ];
        $situacaoRegistroLabels = [
            'aguardando_horario' => 'Aguardando horario',
            'prazo_encerrado' => 'Prazo encerrado',
            'doacao_registrada' => 'Doacao registrada',
            'finalizado' => 'Finalizado',
        ];
        $situacaoRegistroClasses = [
            'aguardando_horario' => 'text-bg-light border',
            'prazo_encerrado' => 'text-bg-secondary',
            'doacao_registrada' => 'text-bg-success',
            'finalizado' => 'text-bg-light border',
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
                @unless ($campanhaTravada)
                    <div class="col-12 col-lg-4">
                        <label class="form-label fw-semibold" for="campanha_id">Campanha</label>
                        <select class="form-select" id="campanha_id" wire:model.live="campanhaId">
                            <option value="">Todas as campanhas</option>
                            @foreach ($campanhas as $campanha)
                                <option value="{{ $campanha->id }}">{{ $campanha->titulo }}</option>
                            @endforeach
                        </select>
                    </div>
                @endunless

                @unless ($campanhaTravada)
                    <div class="col-12 col-lg-4">
                        <label class="form-label fw-semibold" for="local_coleta_id">Local</label>
                        <select class="form-select" id="local_coleta_id" wire:model.live="localColetaId">
                            <option value="">Todos os locais</option>
                            @foreach ($locaisColeta as $local)
                                <option value="{{ $local->id }}">{{ $local->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                @endunless

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

                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label fw-semibold" for="nome_doador">Nome do doador</label>
                    <input class="form-control" id="nome_doador" type="text" placeholder="Buscar por nome" wire:model.live.debounce.500ms="nomeDoador">
                </div>

                <div class="col-12 col-lg-3 d-flex flex-column flex-sm-row align-items-stretch align-items-sm-end justify-content-lg-end gap-2">
                    <button
                        class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-2"
                        type="button"
                        wire:click="limparFiltros"
                        wire:loading.attr="disabled"
                        @disabled(! $filtrosAtivos)
                    >
                        <i class="bi bi-x-lg" aria-hidden="true"></i>
                        Limpar
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

            <div wire:loading.class="opacity-50" wire:target="campanhaId,localColetaId,status,dataInicio,dataFim,nomeDoador,porPagina,limparFiltros,marcarComparecimento,marcarFalta,cancelarOperacionalmente,iniciarRegistroDoacao,cancelarRegistroDoacao,registrarDoacao,previousPage,nextPage,gotoPage">
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
                                <th scope="col">Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($agendamentos as $agendamento)
                                @php
                                    $situacaoRegistro = $agendamento->situacaoRegistroComparecimento();
                                    $corrigindoRegistro = $situacaoRegistro === 'disponivel' && $agendamento->status !== 'agendado';
                                    $registrandoDoacao = $doacaoAgendamentoId === $agendamento->id;
                                @endphp
                                <tr wire:key="agendamento-{{ $agendamento->id }}">
                                    <td>
                                        <strong>{{ $agendamento->data_hora->format('d/m/Y') }}</strong>
                                        <span class="d-block text-secondary small">{{ $agendamento->data_hora->format('H:i') }}</span>
                                    </td>
                                    <td>
                                        <strong>
                                            <a class="text-decoration-none" href="{{ route('admin.agendamentos.show', $agendamento) }}">
                                                {{ $agendamento->user?->name ?? 'Doador removido' }}
                                            </a>
                                        </strong>
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
                                            <span class="badge {{ $doacaoStatusClasses[$agendamento->doacao->status] ?? 'text-bg-light border' }}">
                                                {{ $doacaoStatusLabels[$agendamento->doacao->status] ?? ucfirst($agendamento->doacao->status) }}
                                            </span>
                                            @if ($agendamento->doacao->status === 'confirmada')
                                                <span class="d-block text-secondary small mt-1">
                                                    {{ number_format((int) $agendamento->doacao->quantidade_ml, 0, ',', '.') }} ml
                                                </span>
                                            @elseif ($agendamento->doacao->motivo_recusa)
                                                <span class="d-block text-secondary small mt-1">
                                                    {{ $agendamento->doacao->motivo_recusa }}
                                                </span>
                                            @endif
                                        @elseif ($agendamento->podeRegistrarDoacao())
                                            <button
                                                class="btn btn-sm btn-outline-primary d-inline-flex align-items-center justify-content-center gap-1"
                                                type="button"
                                                wire:click="iniciarRegistroDoacao({{ $agendamento->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="iniciarRegistroDoacao({{ $agendamento->id }})"
                                            >
                                                <i class="bi bi-droplet-half" aria-hidden="true"></i>
                                                Registrar
                                            </button>
                                        @else
                                            <span class="badge text-bg-light border">Sem registro</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($situacaoRegistro === 'disponivel')
                                            @if ($corrigindoRegistro)
                                                <span class="badge text-bg-light border mb-2">Corrigir registro</span>
                                            @endif
                                            <div class="d-grid d-xl-flex gap-2">
                                                @if ($agendamento->status !== 'realizado')
                                                    <button
                                                        class="btn btn-sm btn-outline-success d-inline-flex align-items-center justify-content-center gap-1"
                                                        type="button"
                                                        wire:loading.attr="disabled"
                                                        x-on:click="
                                                            window.confirmAction({
                                                                title: '{{ $corrigindoRegistro ? 'Corrigir para comparecimento?' : 'Registrar comparecimento?' }}',
                                                                text: 'O agendamento sera marcado como realizado e podera seguir para o registro da doacao.',
                                                                confirmButtonText: 'Compareceu',
                                                                buttonColor: '#198754',
                                                                confirmDelayMs: 3000,
                                                            }).then((confirmed) => confirmed && $wire.marcarComparecimento({{ $agendamento->id }}))
                                                        "
                                                    >
                                                        <i class="bi bi-check2-circle" aria-hidden="true"></i>
                                                        Compareceu
                                                    </button>
                                                @endif
                                                @if ($agendamento->status !== 'faltou')
                                                    <button
                                                        class="btn btn-sm btn-outline-warning d-inline-flex align-items-center justify-content-center gap-1"
                                                        type="button"
                                                        wire:loading.attr="disabled"
                                                        x-on:click="
                                                            window.confirmAction({
                                                                title: '{{ $corrigindoRegistro ? 'Corrigir para falta?' : 'Registrar falta?' }}',
                                                                text: 'O agendamento sera marcado como falta do doador.',
                                                                confirmButtonText: 'Faltou',
                                                                buttonColor: '#ffc107',
                                                                confirmDelayMs: 3000,
                                                            }).then((confirmed) => confirmed && $wire.marcarFalta({{ $agendamento->id }}))
                                                        "
                                                    >
                                                        <i class="bi bi-person-x" aria-hidden="true"></i>
                                                        Faltou
                                                    </button>
                                                @endif
                                                @if ($agendamento->status !== 'cancelado')
                                                    <button
                                                        class="btn btn-sm btn-outline-danger d-inline-flex align-items-center justify-content-center gap-1"
                                                        type="button"
                                                        wire:loading.attr="disabled"
                                                        x-on:click="
                                                            window.confirmAction({
                                                                title: '{{ $corrigindoRegistro ? 'Corrigir para cancelamento?' : 'Cancelar atendimento?' }}',
                                                                text: 'O agendamento sera cancelado pela operacao administrativa.',
                                                                confirmButtonText: 'Cancelar',
                                                                buttonColor: '#c62828',
                                                                confirmDelayMs: 3000,
                                                            }).then((confirmed) => confirmed && $wire.cancelarOperacionalmente({{ $agendamento->id }}))
                                                        "
                                                    >
                                                        <i class="bi bi-x-octagon" aria-hidden="true"></i>
                                                        Cancelar
                                                    </button>
                                                @endif
                                            </div>
                                            <span class="d-block text-secondary small mt-2">
                                                Prazo ate {{ $agendamento->prazoRegistroComparecimento()->format('d/m/Y H:i') }}
                                            </span>
                                        @else
                                            <span class="badge {{ $situacaoRegistroClasses[$situacaoRegistro] ?? 'text-bg-light border' }}">
                                                {{ $situacaoRegistroLabels[$situacaoRegistro] ?? 'Indisponivel' }}
                                            </span>

                                            @if ($situacaoRegistro === 'aguardando_horario')
                                                <span class="d-block text-secondary small mt-1">
                                                    Libera em {{ $agendamento->data_hora->format('d/m/Y H:i') }}
                                                </span>
                                            @elseif ($situacaoRegistro === 'prazo_encerrado')
                                                <span class="d-block text-secondary small mt-1">
                                                    Encerrado em {{ $agendamento->prazoRegistroComparecimento()->format('d/m/Y H:i') }}
                                                </span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @if ($registrandoDoacao)
                                    <tr wire:key="agendamento-doacao-{{ $agendamento->id }}">
                                        <td colspan="7">
                                            <div class="border rounded-3 p-3 bg-light-subtle">
                                                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
                                                    <div>
                                                        <h3 class="h6 fw-bold mb-1">Registrar doacao</h3>
                                                        <p class="text-secondary mb-0">
                                                            {{ $agendamento->user?->name ?? 'Doador removido' }} · {{ $agendamento->data_hora->format('d/m/Y H:i') }}
                                                        </p>
                                                    </div>
                                                    <span class="badge text-bg-light border align-self-lg-start">
                                                        A bolsa sera gerada automaticamente se a doacao for confirmada.
                                                    </span>
                                                </div>

                                                <div class="row g-3 align-items-end">
                                                    <div class="col-12 col-lg-3">
                                                        <label class="form-label fw-semibold" for="doacao_status_{{ $agendamento->id }}">Resultado</label>
                                                        <select
                                                            class="form-select @error('doacaoStatus') is-invalid @enderror"
                                                            id="doacao_status_{{ $agendamento->id }}"
                                                            wire:model.live="doacaoStatus"
                                                        >
                                                            @foreach ($statusDoacaoOptions as $statusDoacao)
                                                                <option value="{{ $statusDoacao }}">{{ $doacaoStatusLabels[$statusDoacao] ?? ucfirst($statusDoacao) }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('doacaoStatus')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    @if ($doacaoStatus === 'confirmada')
                                                        <div class="col-12 col-lg-3">
                                                            <label class="form-label fw-semibold" for="quantidade_ml_{{ $agendamento->id }}">Quantidade coletada</label>
                                                            <div class="input-group">
                                                                <input
                                                                    class="form-control @error('quantidadeMl') is-invalid @enderror"
                                                                    id="quantidade_ml_{{ $agendamento->id }}"
                                                                    type="number"
                                                                    min="1"
                                                                    max="1000"
                                                                    step="1"
                                                                    wire:model="quantidadeMl"
                                                                >
                                                                <span class="input-group-text">ml</span>
                                                                @error('quantidadeMl')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="col-12 col-lg-6">
                                                            <label class="form-label fw-semibold" for="motivo_recusa_{{ $agendamento->id }}">Motivo da recusa</label>
                                                            <textarea
                                                                class="form-control @error('motivoRecusa') is-invalid @enderror"
                                                                id="motivo_recusa_{{ $agendamento->id }}"
                                                                rows="2"
                                                                maxlength="5000"
                                                                wire:model="motivoRecusa"
                                                            ></textarea>
                                                            @error('motivoRecusa')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    @endif

                                                    <div class="col-12 col-lg d-flex flex-column flex-sm-row justify-content-lg-end gap-2">
                                                        <button
                                                            class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-2"
                                                            type="button"
                                                            wire:click="cancelarRegistroDoacao"
                                                            wire:loading.attr="disabled"
                                                        >
                                                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                                                            Cancelar
                                                        </button>
                                                        <button
                                                            class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-2"
                                                            type="button"
                                                            wire:loading.attr="disabled"
                                                            x-on:click="
                                                                window.confirmAction({
                                                                    title: 'Registrar doacao?',
                                                                    text: 'Confira o resultado informado antes de confirmar.',
                                                                    confirmButtonText: 'Registrar doacao',
                                                                    buttonColor: '#0d6efd',
                                                                    confirmDelayMs: 3000,
                                                                }).then((confirmed) => confirmed && $wire.registrarDoacao())
                                                            "
                                                        >
                                                            <i class="bi bi-check-lg" aria-hidden="true"></i>
                                                            Registrar doacao
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td class="text-center text-secondary py-4" colspan="7">
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

<div>
<article class="card shadow-sm rounded-3 mb-4">
    <div class="card-header bg-white border-bottom-0 p-4" data-bs-toggle="collapse" data-bs-target="#collapseEstoque" aria-expanded="false" aria-controls="collapseEstoque" style="cursor: pointer;">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <h2 class="h5 fw-bold mb-1">Estoque calculado <i class="bi bi-chevron-down fs-6 ms-2"></i></h2>
                <p class="text-secondary mb-0">Somente bolsas disponiveis ou transferidas e dentro da validade entram no saldo.</p>
            </div>
            <span class="badge text-bg-light border align-self-start">
                {{ $totalBolsasDisponiveis ?? 0 }} bolsas disponiveis
            </span>
        </div>
    </div>

    <div class="collapse" id="collapseEstoque" wire:ignore.self>
        <div class="card-body p-4 pt-0">
            <div class="row g-3 mb-4">
                <div class="col-12 col-lg-5">
                    <label class="form-label fw-semibold" for="local_estoque">Local</label>
                    <select class="form-select" id="local_estoque" wire:model.live="localId">
                        <option value="">Todos os locais</option>
                        @foreach ($locais as $local)
                            <option value="{{ $local->id }}">{{ $local->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-4">
                    <label class="form-label fw-semibold" for="tipo_estoque">Tipo sanguineo</label>
                    <select class="form-select" id="tipo_estoque" wire:model.live="tipoSanguineo">
                        <option value="">Todos os tipos</option>
                        @foreach ($tiposSanguineos as $tipo)
                            <option value="{{ $tipo }}">{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-end">
                    <button
                        class="btn btn-outline-secondary w-100"
                        type="button"
                        wire:click="limparFiltrosEstoque"
                        wire:loading.attr="disabled"
                        @disabled($localId === '' && $tipoSanguineo === '')
                    >
                        Limpar filtros
                    </button>
                </div>
            </div>

            <div class="table-responsive" wire:loading.class="opacity-50" wire:target="localId,tipoSanguineo,limparFiltrosEstoque,atualizarEstoqueMinimo">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Local</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Bolsas</th>
                            <th scope="col">Quantidade</th>
                            <th scope="col">Minimo</th>
                            <th scope="col">Situacao</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($estoques as $estoque)
                            <tr>
                                <td>{{ $estoque['local']->nome }}</td>
                                <td><strong>{{ $estoque['tipo_sanguineo'] }}</strong></td>
                                <td>{{ $estoque['total_bolsas'] }}</td>
                                <td>{{ number_format($estoque['quantidade_ml'], 0, ',', '.') }} ml</td>
                                <td>
                                    <div class="input-group input-group-sm" x-data="{ minimo: {{ $estoque['estoque_minimo_ml'] }} }">
                                        <input
                                            class="form-control"
                                            type="number"
                                            min="0"
                                            max="1000000"
                                            step="50"
                                            x-model.number="minimo"
                                            aria-label="Estoque minimo em mililitros"
                                        >
                                        <span class="input-group-text">ml</span>
                                        <button
                                            class="btn btn-outline-primary"
                                            type="button"
                                            wire:loading.attr="disabled"
                                            x-on:click="$wire.atualizarEstoqueMinimo({{ $estoque['id'] }}, minimo)"
                                        >
                                            Salvar
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $estoque['abaixo_minimo'] ? 'text-bg-danger' : 'text-bg-success' }}">
                                        {{ $estoque['abaixo_minimo'] ? 'Abaixo do minimo' : 'Adequado' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center text-secondary py-4" colspan="6">Nenhuma configuracao de estoque encontrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($estoques->hasPages())
                <div class="mt-4">
                    {{ $estoques->links() }}
                </div>
            @endif
        </div>
    </div>
</article>

<article class="card shadow-sm rounded-3">
    <div class="card-header bg-white border-bottom-0 p-4" data-bs-toggle="collapse" data-bs-target="#collapseBolsas" aria-expanded="false" aria-controls="collapseBolsas" style="cursor: pointer;">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <h2 class="h5 fw-bold mb-1">Bolsas registradas <i class="bi bi-chevron-down fs-6 ms-2"></i></h2>
                <p class="text-secondary mb-0">Utilize, descarte ou transfira bolsas ainda disponiveis.</p>
            </div>
            <span class="badge text-bg-light border align-self-start">
                {{ $bolsas->total() }} {{ $bolsas->total() === 1 ? 'bolsa' : 'bolsas' }}
            </span>
        </div>
    </div>

    <div class="collapse" id="collapseBolsas" wire:ignore.self>
        <div class="card-body p-4 pt-0">

            <div class="row g-3 mb-4">
                <div class="col-12 col-lg-4">
                    <label class="form-label fw-semibold" for="local_bolsa">Local</label>
                    <select class="form-select" id="local_bolsa" wire:model.live="localId">
                        <option value="">Todos os locais</option>
                        @foreach ($locais as $local)
                            <option value="{{ $local->id }}">{{ $local->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label fw-semibold" for="tipo_bolsa">Tipo sanguineo</label>
                    <select class="form-select" id="tipo_bolsa" wire:model.live="tipoSanguineo">
                        <option value="">Todos os tipos</option>
                        @foreach ($tiposSanguineos as $tipo)
                            <option value="{{ $tipo }}">{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label fw-semibold" for="status_bolsa">Status</label>
                    <select class="form-select" id="status_bolsa" wire:model.live="status">
                        <option value="">Todos os status</option>
                        <option value="disponivel">Disponivel</option>
                        <option value="transferida">Transferida</option>
                        <option value="utilizada">Utilizada</option>
                        <option value="descartada">Descartada</option>
                        <option value="vencida">Vencida</option>
                    </select>
                </div>
                <div class="col-12 col-lg-2 d-flex align-items-end">
                    <button
                        class="btn btn-outline-secondary w-100"
                        type="button"
                        wire:click="limparFiltros"
                        wire:loading.attr="disabled"
                        @disabled($localId === '' && $tipoSanguineo === '' && $status === '')
                    >
                        Limpar filtros
                    </button>
                </div>
            </div>

            <div wire:loading.class="opacity-50" wire:target="localId,tipoSanguineo,status,limparFiltros,utilizar,descartar,transferir">
                @forelse ($bolsas as $bolsa)
                    @php
                        $statusAtual = $bolsa->statusAtual();
                        $statusClasses = [
                            'disponivel' => 'text-bg-success',
                            'transferida' => 'text-bg-primary',
                            'utilizada' => 'text-bg-secondary',
                            'descartada' => 'text-bg-dark',
                            'vencida' => 'text-bg-danger',
                        ];
                    @endphp

                    <div class="border rounded-3 p-3 mb-3" wire:key="bolsa-{{ $bolsa->id }}">
                        <div class="d-flex flex-column flex-xl-row justify-content-between gap-3">
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                    <h3 class="h6 fw-bold mb-0">Bolsa #{{ $bolsa->id }}</h3>
                                    <span class="badge {{ $statusClasses[$statusAtual] }}">
                                        {{ ucfirst($statusAtual) }}
                                    </span>
                                    <span class="badge text-bg-light border">{{ $bolsa->tipo_sanguineo }}</span>
                                </div>
                                <p class="text-secondary mb-2">
                                    {{ $bolsa->localColeta->nome }} · {{ number_format($bolsa->quantidade_ml, 0, ',', '.') }} ml
                                </p>
                                <div class="d-flex flex-wrap gap-2 small text-secondary">
                                    <span>Doador: {{ $bolsa->doacao->agendamento->user->name }}</span>
                                    <span>Coleta: {{ $bolsa->data_coleta->format('d/m/Y H:i') }}</span>
                                    <span>Validade: {{ $bolsa->validade_em->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>

                            @if ($bolsa->estaDisponivel())
                                <div class="d-grid d-sm-flex flex-xl-column align-items-stretch gap-2" x-data="{ destino: '' }">
                                    <button
                                        class="btn btn-outline-success"
                                        type="button"
                                        wire:loading.attr="disabled"
                                        x-on:click="
                                            window.confirmAction({
                                                title: 'Registrar utilizacao?',
                                                text: 'A bolsa deixara de compor o estoque disponivel.',
                                                confirmButtonText: 'Registrar utilizacao',
                                                buttonColor: '#198754',
                                            }).then((confirmed) => confirmed && $wire.utilizar({{ $bolsa->id }}))
                                        "
                                    >
                                        Utilizar
                                    </button>
                                    <button
                                        class="btn btn-outline-danger"
                                        type="button"
                                        wire:loading.attr="disabled"
                                        x-on:click="
                                            window.confirmAction({
                                                title: 'Descartar bolsa?',
                                                text: 'A bolsa deixara de compor o estoque disponivel.',
                                                confirmButtonText: 'Descartar',
                                            }).then((confirmed) => confirmed && $wire.descartar({{ $bolsa->id }}))
                                        "
                                    >
                                        Descartar
                                    </button>
                                    <div class="input-group">
                                        <select class="form-select" x-model="destino" aria-label="Local de destino">
                                            <option value="">Transferir para...</option>
                                            @foreach ($locais as $local)
                                                @if ($local->id !== $bolsa->local_coleta_id)
                                                    <option value="{{ $local->id }}">{{ $local->nome }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <button
                                            class="btn btn-outline-primary"
                                            type="button"
                                            wire:loading.attr="disabled"
                                            x-bind:disabled="destino === ''"
                                            x-on:click="
                                                window.confirmAction({
                                                    title: 'Transferir bolsa?',
                                                    text: 'A bolsa passara a compor o estoque do local de destino.',
                                                    confirmButtonText: 'Transferir',
                                                    buttonColor: '#0d6efd',
                                                }).then((confirmed) => confirmed && $wire.transferir({{ $bolsa->id }}, Number(destino)))
                                            "
                                        >
                                            Transferir
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="border rounded-3 p-4 text-center">
                        <h3 class="h6 fw-bold mb-1">Nenhuma bolsa encontrada</h3>
                        <p class="text-secondary mb-0">Revise os filtros ou registre uma doacao confirmada.</p>
                    </div>
                @endforelse
            </div>

            @if ($bolsas->hasPages())
                <div class="mt-4">
                    {{ $bolsas->links() }}
                </div>
            @endif
        </div>
    </div>
    </article>
</div>

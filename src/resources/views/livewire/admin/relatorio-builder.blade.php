<div>
    <div class="row g-4">
        <!-- Controles Laterais (Filtros e Configurações) -->
        <div class="col-lg-3">
            <div class="card shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title fw-bold mb-0">Configurar Relatório</h5>
                </div>
                <div class="card-body">
                    <!-- Tópico -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Tipo de Relatório (Múltiplo)</label>
                        <div class="d-flex flex-column gap-1">
                            @foreach ($this->getModulos() as $chave => $nome)
                                <div class="form-check">
                                    <input 
                                        wire:model.live="modulosSelecionados" 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        value="{{ $chave }}" 
                                        id="modulo_{{ $chave }}"
                                    >
                                    <label class="form-check-label" for="modulo_{{ $chave }}">
                                        {{ $nome }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <hr class="text-secondary">

                    <!-- Colunas -->
                    <div class="mb-4">
                        <h6 class="fw-semibold mb-3">Colunas Visíveis</h6>
                        
                        @if(empty($modulosSelecionados))
                            <p class="small text-secondary">Selecione ao menos um tipo de relatório acima.</p>
                        @else
                            @foreach ($modulosSelecionados as $modulo)
                                <div class="mb-3">
                                    <span class="d-block small fw-bold text-primary mb-2">{{ $this->getModulos()[$modulo] }}</span>
                                    <div class="d-flex flex-column gap-1">
                                        @foreach ($this->getOpcoesColunas($modulo) as $chave => $label)
                                            <div class="form-check">
                                                <input 
                                                    wire:model.live="colunasSelecionadas.{{ $modulo }}" 
                                                    class="form-check-input" 
                                                    type="checkbox" 
                                                    value="{{ $chave }}" 
                                                    id="coluna_{{ $modulo }}_{{ $chave }}"
                                                >
                                                <label class="form-check-label small" for="coluna_{{ $modulo }}_{{ $chave }}">
                                                    {{ $label }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <hr class="text-secondary">

                    <!-- Filtros Dinâmicos -->
                    <div class="mb-3">
                        <h6 class="fw-semibold mb-3">Filtros Combinados</h6>
                        
                        @php
                            $filtrosDisponiveis = $this->getFiltrosDisponiveis();
                        @endphp

                        @if(empty($filtrosDisponiveis))
                            <p class="small text-secondary">Selecione um módulo para exibir filtros.</p>
                        @endif

                        @if (in_array('datas', $filtrosDisponiveis))
                            <div class="mb-3">
                                <label class="form-label small text-secondary fw-semibold">Data Inicial</label>
                                <input wire:model.live="filtroDataInicio" type="date" class="form-control form-control-sm">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-secondary fw-semibold">Data Final</label>
                                <input wire:model.live="filtroDataFim" type="date" class="form-control form-control-sm">
                            </div>
                        @endif

                        @if (in_array('local', $filtrosDisponiveis))
                            <div class="mb-3">
                                <label class="form-label small text-secondary fw-semibold">Local de Coleta</label>
                                <div class="d-flex flex-column gap-1" style="max-height: 150px; overflow-y: auto;">
                                    @foreach ($this->getLocaisColeta() as $id => $nome)
                                        <div class="form-check">
                                            <input wire:model.live="filtroLocalColeta" class="form-check-input" type="checkbox" value="{{ $id }}" id="local_{{ $id }}">
                                            <label class="form-check-label small" for="local_{{ $id }}">{{ $nome }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if (in_array('status_agendamento', $filtrosDisponiveis))
                            <div class="mb-3">
                                <label class="form-label small text-secondary fw-semibold">Status do Agendamento</label>
                                <div class="d-flex flex-column gap-1">
                                    @foreach ($this->getStatusAgendamento() as $chave => $label)
                                        <div class="form-check">
                                            <input wire:model.live="filtroStatusAgendamento" class="form-check-input" type="checkbox" value="{{ $chave }}" id="status_agendamento_{{ $chave }}">
                                            <label class="form-check-label small" for="status_agendamento_{{ $chave }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if (in_array('status_bolsa', $filtrosDisponiveis))
                            <div class="mb-3">
                                <label class="form-label small text-secondary fw-semibold">Status da Bolsa</label>
                                <div class="d-flex flex-column gap-1">
                                    @foreach ($this->getStatusBolsa() as $chave => $label)
                                        <div class="form-check">
                                            <input wire:model.live="filtroStatusBolsa" class="form-check-input" type="checkbox" value="{{ $chave }}" id="status_bolsa_{{ $chave }}">
                                            <label class="form-check-label small" for="status_bolsa_{{ $chave }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if (in_array('status_campanha', $filtrosDisponiveis))
                            <div class="mb-3">
                                <label class="form-label small text-secondary fw-semibold">Status da Campanha</label>
                                <div class="d-flex flex-column gap-1">
                                    @foreach ($this->getStatusCampanha() as $chave => $label)
                                        <div class="form-check">
                                            <input wire:model.live="filtroStatusCampanha" class="form-check-input" type="checkbox" value="{{ $chave }}" id="status_campanha_{{ $chave }}">
                                            <label class="form-check-label small" for="status_campanha_{{ $chave }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if (in_array('tipo_sanguineo', $filtrosDisponiveis))
                            <div class="mb-3">
                                <label class="form-label small text-secondary fw-semibold">Tipo Sanguíneo</label>
                                <div class="d-flex flex-column gap-1">
                                    @foreach ($this->getTiposSanguineos() as $tipo)
                                        <div class="form-check">
                                            <input wire:model.live="filtroTipoSanguineo" class="form-check-input" type="checkbox" value="{{ $tipo }}" id="tipo_{{ Str::slug($tipo) }}">
                                            <label class="form-check-label small" for="tipo_{{ Str::slug($tipo) }}">{{ $tipo }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

        <!-- Área de Visualização e Exportação -->
        <div class="col-lg-9">
            <div class="card shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <h5 class="card-title fw-bold mb-0">Visualização do Relatório (Consolidado)</h5>
                    <div class="d-flex gap-2">
                        <button wire:click="toggleMostrarTudo" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" wire:loading.attr="disabled" wire:target="toggleMostrarTudo">
                            <i class="bi bi-arrows-expand" wire:loading.remove wire:target="toggleMostrarTudo" aria-hidden="true"></i>
                            <span class="spinner-border spinner-border-sm" wire:loading wire:target="toggleMostrarTudo" role="status" aria-hidden="true"></span>
                            {{ $mostrarTudo ? 'Recolher' : 'Mostrar tudo' }}
                        </button>
                        <button wire:click="exportarCsv" class="btn btn-outline-success d-inline-flex align-items-center gap-2" wire:loading.attr="disabled" wire:target="exportarCsv" @if(empty($modulosSelecionados)) disabled @endif>
                            <i class="bi bi-filetype-csv" wire:loading.remove wire:target="exportarCsv" aria-hidden="true"></i>
                            <span class="spinner-border spinner-border-sm" wire:loading wire:target="exportarCsv" role="status" aria-hidden="true"></span>
                            Exportar CSV
                        </button>
                        <button wire:click="exportarPdf" class="btn btn-primary d-inline-flex align-items-center gap-2" wire:loading.attr="disabled" wire:target="exportarPdf" @if(empty($modulosSelecionados)) disabled @endif>
                            <i class="bi bi-file-earmark-pdf" wire:loading.remove wire:target="exportarPdf" aria-hidden="true"></i>
                            <span class="spinner-border spinner-border-sm" wire:loading wire:target="exportarPdf" role="status" aria-hidden="true"></span>
                            Gerar PDF
                        </button>
                    </div>
                </div>
                <div class="card-body p-0" x-data="{ height: 600, resizing: false, startY: 0, startHeight: 0 }">
                    <div class="p-4" :style="{{ $mostrarTudo ? 'true' : 'false' }} ? '' : `height: ${height}px; min-height: 300px; overflow-y: auto;`">
                        @if (empty($modulosSelecionados))
                            <div class="p-5 text-center text-secondary">
                                <i class="bi bi-list-check fs-1 mb-3 d-block text-muted"></i>
                                <p>Selecione ao menos um módulo de relatório na barra lateral.</p>
                            </div>
                        @else
                            @foreach($modulosSelecionados as $modulo)
                                @php
                                    $colunas = $colunasSelecionadas[$modulo] ?? [];
                                @endphp
                                
                                <div class="mb-5">
                                    <h5 class="fw-bold text-primary mb-3">{{ $this->getModulos()[$modulo] }}</h5>
                                    
                                    @if (empty($colunas))
                                        <div class="p-4 text-center border rounded bg-light text-secondary mb-4">
                                            <i class="bi bi-layout-three-columns fs-3 mb-2 d-block text-muted"></i>
                                            <p class="mb-0">Selecione ao menos uma coluna para este módulo.</p>
                                        </div>
                                    @else
                                        <div class="table-responsive border rounded-3">
                                            <table class="table table-hover table-striped mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        @foreach ($this->getOpcoesColunas($modulo) as $chave => $label)
                                                            @if (in_array($chave, $colunas))
                                                                <th scope="col" class="text-nowrap border-bottom">{{ $label }}</th>
                                                            @endif
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($dadosPorModulo[$modulo] as $linha)
                                                        <tr>
                                                            @foreach ($this->getOpcoesColunas($modulo) as $chave => $label)
                                                                @if (in_array($chave, $colunas))
                                                                    <td>{{ $this->formatarValor($linha, $modulo, $chave) }}</td>
                                                                @endif
                                                            @endforeach
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="{{ count($colunas) }}" class="text-center py-4 text-secondary">
                                                                Nenhum registro encontrado com os filtros atuais.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                    
                    @if(!$mostrarTudo)
                    <!-- Barra de Redimensionamento Customizada -->
                    <div 
                        x-data="{ hover: false }"
                        class="border-top d-flex justify-content-center align-items-center" 
                        :class="hover || resizing ? 'bg-secondary text-white' : 'bg-light text-secondary'"
                        style="cursor: ns-resize; height: 24px; user-select: none; transition: all 0.2s; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem;"
                        @mouseenter="hover = true"
                        @mouseleave="hover = false"
                        @mousedown="resizing = true; startY = $event.clientY; startHeight = height; document.body.style.userSelect = 'none';"
                        @mousemove.window="if(resizing) { height = Math.max(300, startHeight + ($event.clientY - startY)) }"
                        @mouseup.window="resizing = false; document.body.style.userSelect = '';"
                    >
                        <i class="bi bi-grip-horizontal"></i>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

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

                    <div class="mb-4">
                        <h6 class="fw-semibold mb-3">Conteúdo Analítico no PDF</h6>

                        <div class="form-check mb-3">
                            <input
                                wire:model.live="incluirIndicadores"
                                class="form-check-input"
                                type="checkbox"
                                id="incluir_indicadores_pdf"
                            >
                            <label class="form-check-label small" for="incluir_indicadores_pdf">
                                Incluir indicadores
                            </label>
                        </div>

                        <span class="d-block small fw-bold text-primary mb-2">Gráficos no PDF</span>
                        <div class="d-flex flex-column gap-1">
                            @foreach ($this->getGraficosPrincipais() as $chave => $label)
                                <div class="form-check">
                                    <input
                                        wire:model.live="graficosSelecionados"
                                        class="form-check-input"
                                        type="checkbox"
                                        value="{{ $chave }}"
                                        id="grafico_pdf_{{ $chave }}"
                                    >
                                    <label class="form-check-label small" for="grafico_pdf_{{ $chave }}">
                                        {{ $label }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
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
                @if ($exportacoesPdf->isNotEmpty())
                    <div class="border-bottom bg-white p-4" wire:poll.10s>
                        <div class="d-flex flex-column flex-xl-row justify-content-between gap-2 mb-3">
                            <div>
                                <h6 class="fw-bold mb-1">Exportações de PDF</h6>
                                <p class="text-secondary small mb-0">Últimos PDFs solicitados por este administrador.</p>
                            </div>
                            <span class="badge text-bg-light border align-self-start">
                                <i class="bi bi-hourglass-split me-1" aria-hidden="true"></i>
                                Fila
                            </span>
                        </div>

                        <div class="d-flex flex-column gap-2">
                            @foreach ($exportacoesPdf as $exportacao)
                                <div class="border rounded-3 p-3 d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-3">
                                    <div class="d-flex flex-column gap-1">
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <span class="badge {{ $exportacao->statusBadge() }}">{{ $exportacao->statusLabel() }}</span>
                                            <span class="fw-semibold">PDF #{{ $exportacao->id }}</span>
                                        </div>
                                        <span class="text-secondary small">
                                            Solicitado em {{ $exportacao->created_at->format('d/m/Y H:i') }}
                                            @if ($exportacao->finished_at)
                                                - finalizado em {{ $exportacao->finished_at->format('d/m/Y H:i') }}
                                            @endif
                                        </span>
                                        @if ($exportacao->status === \App\Models\RelatorioExport::STATUS_FALHOU && $exportacao->erro)
                                            <span class="text-danger small">{{ $exportacao->erro }}</span>
                                        @endif
                                    </div>

                                    <div class="d-flex align-items-center gap-2">
                                        @if ($exportacao->concluido())
                                            <a
                                                href="{{ route('admin.relatorios.exports.download', $exportacao) }}"
                                                class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-2"
                                            >
                                                <i class="bi bi-download" aria-hidden="true"></i>
                                                Baixar
                                            </a>
                                            <button
                                                wire:click="arquivarPdf({{ $exportacao->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="arquivarPdf({{ $exportacao->id }})"
                                                class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-2"
                                                title="Arquivar (compactar arquivo e ocultar da lista)"
                                            >
                                                <i class="bi bi-archive" aria-hidden="true"></i>
                                                Arquivar
                                            </button>
                                            <button
                                                wire:click="excluirPdf({{ $exportacao->id }})"
                                                wire:confirm="Tem certeza que deseja excluir permanentemente este relatório? O arquivo será apagado do servidor."
                                                wire:loading.attr="disabled"
                                                wire:target="excluirPdf({{ $exportacao->id }})"
                                                class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-2"
                                                title="Excluir arquivo permanentemente"
                                            >
                                                <i class="bi bi-trash" aria-hidden="true"></i>
                                                Excluir
                                            </button>
                                        @elseif (in_array($exportacao->status, [\App\Models\RelatorioExport::STATUS_PROCESSANDO, \App\Models\RelatorioExport::STATUS_ARQUIVANDO, \App\Models\RelatorioExport::STATUS_DESARQUIVANDO]))
                                            <span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            <a href="{{ route('admin.relatorios.meus-relatorios') }}" class="btn btn-link text-decoration-none d-inline-flex align-items-center gap-2">
                                Ver todos os relatórios <i class="bi bi-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                @endif
                <div class="border-bottom bg-light-subtle p-4">
                    <div class="d-flex flex-column flex-xl-row justify-content-between gap-2 mb-4">
                        <div>
                            <h6 class="fw-bold mb-1">Painel analítico</h6>
                            <p class="text-secondary small mb-0">Indicadores calculados com os módulos e filtros selecionados.</p>
                        </div>
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-2">
                            <label class="form-label small text-secondary fw-semibold mb-0" for="grafico_principal">
                                Métrica do gráfico
                            </label>
                            <select
                                class="form-select form-select-sm"
                                id="grafico_principal"
                                wire:model.live="graficoPrincipal"
                            >
                                @foreach ($this->getGraficosPrincipais() as $chave => $label)
                                    <option value="{{ $chave }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="border rounded-3 bg-white p-4 mb-4">
                        <div class="d-flex flex-column flex-xl-row justify-content-between gap-3 mb-3">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $graficoPrincipalData['titulo'] }}</h6>
                                <p class="text-secondary small mb-0">{{ $graficoPrincipalData['descricao'] }}</p>
                            </div>
                            <span class="badge text-bg-light border align-self-start">
                                <i class="bi bi-funnel me-1" aria-hidden="true"></i>
                                Atualiza com os filtros
                            </span>
                        </div>

                        @if ($graficoPrincipalData['vazio'])
                            <div class="text-center text-secondary border rounded-3 bg-light p-5">
                                <i class="bi bi-bar-chart-line fs-2 d-block mb-2 text-muted"></i>
                                Não há dados suficientes para montar este gráfico com os filtros atuais.
                            </div>
                        @else
                            <div
                                wire:key="grafico-principal-{{ $graficoPrincipalData['key'] }}"
                                data-report-chart-root
                                x-data
                                x-init="$nextTick(() => window.renderReportChart($el))"
                            >
                                <div style="height: 360px;">
                                    <canvas data-report-chart aria-label="{{ $graficoPrincipalData['titulo'] }}" role="img"></canvas>
                                </div>
                                <script type="application/json" data-report-chart-config>@json($graficoPrincipalData['chart'])</script>
                            </div>
                        @endif
                    </div>

                    @if (empty($painelAnalitico['cards']))
                        <div class="text-center text-secondary border rounded-3 bg-white p-4">
                            <i class="bi bi-bar-chart-line fs-2 d-block mb-2 text-muted"></i>
                            Selecione ao menos um módulo para visualizar indicadores.
                        </div>
                    @else
                        <div class="row g-3 mb-4">
                            @foreach ($painelAnalitico['cards'] as $card)
                                <div class="col-md-6 col-xl-4">
                                    <div class="border rounded-3 bg-white p-3 h-100">
                                        <div class="d-flex align-items-start justify-content-between gap-3">
                                            <div>
                                                <span class="text-secondary small fw-semibold">{{ $card['titulo'] }}</span>
                                                <strong class="fs-4 d-block lh-sm mt-1">{{ $card['valor'] }}</strong>
                                                <span class="text-secondary small">{{ $card['detalhe'] }}</span>
                                            </div>
                                            <span class="bg-danger-subtle text-danger border border-danger-subtle rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 2.25rem; height: 2.25rem;">
                                                <i class="bi {{ $card['icone'] }}" aria-hidden="true"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="row g-4">
                            @if (! empty($painelAnalitico['statusAgendamentos']))
                                <div class="col-xl-6">
                                    <div class="border rounded-3 bg-white p-4 h-100">
                                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                            <div>
                                                <h6 class="fw-bold mb-1">Agendamentos por status</h6>
                                                <p class="text-secondary small mb-0">Distribuição operacional dos agendamentos.</p>
                                            </div>
                                            <i class="bi bi-pie-chart text-danger fs-4" aria-hidden="true"></i>
                                        </div>

                                        <div class="d-flex flex-column gap-3">
                                            @foreach ($painelAnalitico['statusAgendamentos'] as $item)
                                                <div>
                                                    <div class="d-flex justify-content-between small mb-1">
                                                        <span class="fw-semibold">{{ $item['label'] }}</span>
                                                        <span class="text-secondary">{{ $item['total'] }}</span>
                                                    </div>
                                                    <div class="progress" style="height: 0.75rem;" role="progressbar" aria-valuenow="{{ $item['percentual'] }}" aria-valuemin="0" aria-valuemax="100">
                                                        <div class="progress-bar {{ $item['classe'] }}" style="width: {{ $item['percentual'] }}%;"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (! empty($painelAnalitico['evolucaoAgendamentos']))
                                <div class="col-xl-6">
                                    <div class="border rounded-3 bg-white p-4 h-100">
                                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                            <div>
                                                <h6 class="fw-bold mb-1">Evolução recente</h6>
                                                <p class="text-secondary small mb-0">Agendamentos e doações confirmadas por data.</p>
                                            </div>
                                            <i class="bi bi-graph-up-arrow text-danger fs-4" aria-hidden="true"></i>
                                        </div>

                                        <div class="d-flex flex-column gap-3">
                                            @foreach ($painelAnalitico['evolucaoAgendamentos'] as $item)
                                                <div>
                                                    <div class="d-flex justify-content-between small mb-1">
                                                        <span class="fw-semibold">{{ $item['label'] }}</span>
                                                        <span class="text-secondary">{{ $item['agendamentos'] }} ag. / {{ $item['doacoes'] }} doaç.</span>
                                                    </div>
                                                    <div class="progress" style="height: 0.75rem;" role="progressbar" aria-valuenow="{{ $item['percentual'] }}" aria-valuemin="0" aria-valuemax="100">
                                                        <div class="progress-bar bg-danger" style="width: {{ $item['percentual'] }}%;"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (! empty($painelAnalitico['campanhas']))
                                <div class="col-xl-6">
                                    <div class="border rounded-3 bg-white p-4 h-100">
                                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                            <div>
                                                <h6 class="fw-bold mb-1">Desempenho das campanhas</h6>
                                                <p class="text-secondary small mb-0">Ranking por doações confirmadas e avanço da meta.</p>
                                            </div>
                                            <i class="bi bi-trophy text-danger fs-4" aria-hidden="true"></i>
                                        </div>

                                        <div class="d-flex flex-column gap-3">
                                            @foreach ($painelAnalitico['campanhas'] as $campanha)
                                                <div>
                                                    <div class="d-flex justify-content-between gap-3 small mb-1">
                                                        <span class="fw-semibold text-truncate">{{ $campanha['titulo'] }}</span>
                                                        <span class="text-secondary flex-shrink-0">{{ $campanha['doacoes'] }}/{{ $campanha['meta'] }} bolsas</span>
                                                    </div>
                                                    <div class="progress mb-1" style="height: 0.75rem;" role="progressbar" aria-valuenow="{{ $campanha['percentual'] }}" aria-valuemin="0" aria-valuemax="100">
                                                        <div class="progress-bar bg-success" style="width: {{ $campanha['percentual'] }}%;"></div>
                                                    </div>
                                                    <span class="text-secondary small">{{ $campanha['agendamentos'] }} agendamentos</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (! empty($painelAnalitico['estoque']))
                                <div class="col-xl-6">
                                    <div class="border rounded-3 bg-white p-4 h-100">
                                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                            <div>
                                                <h6 class="fw-bold mb-1">Estoque disponível por tipo</h6>
                                                <p class="text-secondary small mb-0">Volume disponível considerando bolsas válidas.</p>
                                            </div>
                                            <i class="bi bi-droplet-half text-danger fs-4" aria-hidden="true"></i>
                                        </div>

                                        <div class="d-flex flex-column gap-3">
                                            @foreach ($painelAnalitico['estoque'] as $item)
                                                <div>
                                                    <div class="d-flex justify-content-between small mb-1">
                                                        <span class="fw-semibold">{{ $item['tipo'] }}</span>
                                                        <span class="text-secondary">{{ number_format($item['ml'], 0, ',', '.') }} ml / {{ $item['bolsas'] }} bolsas</span>
                                                    </div>
                                                    <div class="progress" style="height: 0.75rem;" role="progressbar" aria-valuenow="{{ $item['percentual'] }}" aria-valuemin="0" aria-valuemax="100">
                                                        <div class="progress-bar bg-danger" style="width: {{ $item['percentual'] }}%;"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (! empty($painelAnalitico['doadores']))
                                <div class="col-xl-6">
                                    <div class="border rounded-3 bg-white p-4 h-100">
                                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                            <div>
                                                <h6 class="fw-bold mb-1">Doadores por tipo sanguíneo</h6>
                                                <p class="text-secondary small mb-0">Distribuição dos cadastros selecionados.</p>
                                            </div>
                                            <i class="bi bi-people text-danger fs-4" aria-hidden="true"></i>
                                        </div>

                                        <div class="d-flex flex-column gap-3">
                                            @foreach ($painelAnalitico['doadores'] as $item)
                                                <div>
                                                    <div class="d-flex justify-content-between small mb-1">
                                                        <span class="fw-semibold">{{ $item['tipo'] }}</span>
                                                        <span class="text-secondary">{{ $item['total'] }}</span>
                                                    </div>
                                                    <div class="progress" style="height: 0.75rem;" role="progressbar" aria-valuenow="{{ $item['percentual'] }}" aria-valuemin="0" aria-valuemax="100">
                                                        <div class="progress-bar bg-primary" style="width: {{ $item['percentual'] }}%;"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
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

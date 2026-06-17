<div>
    <section class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Meus Relatórios</h4>
            <p class="text-secondary mb-0">Gerencie todos os seus relatórios exportados.</p>
        </div>
        <a href="{{ route('admin.relatorios.index') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            Voltar para o Relatório
        </a>
    </div>

    <div class="card shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex gap-2 align-items-center">
                <input type="checkbox" wire:model.live="selecionarTodos" class="form-check-input mt-0 me-2" title="Selecionar todos nesta página">
                <span class="text-secondary small">{{ count($selecionados) }} selecionado(s)</span>
            </div>
            
            <div class="d-flex gap-2">
                <span class="d-inline-block" @if(!$this->podeArquivar && !empty($selecionados)) data-bs-toggle="tooltip" data-bs-title="Selecione apenas relatórios concluídos e não arquivados para usar esta ação." @endif>
                    <button wire:click="arquivarEmMassa" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-2" style="{{ !$this->podeArquivar ? 'pointer-events: none;' : '' }}" @if(!$this->podeArquivar) disabled @endif>
                        <i class="bi bi-archive"></i> Arquivar
                    </button>
                </span>
                
                <span class="d-inline-block" @if(!$this->podeDesarquivar && !empty($selecionados)) data-bs-toggle="tooltip" data-bs-title="Selecione apenas relatórios que estão arquivados." @endif>
                    <button wire:click="desarquivarEmMassa" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-2" style="{{ !$this->podeDesarquivar ? 'pointer-events: none;' : '' }}" @if(!$this->podeDesarquivar) disabled @endif>
                        <i class="bi bi-box-arrow-up"></i> Resgatar
                    </button>
                </span>
                
                <span class="d-inline-block" @if(!$this->podeExcluir && !empty($selecionados)) data-bs-toggle="tooltip" data-bs-title="Alguns relatórios selecionados estão em processamento e não podem ser excluídos." @endif>
                    <button wire:click="excluirEmMassa" wire:confirm="Tem certeza que deseja excluir permanentemente os relatórios selecionados?" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-2" style="{{ !$this->podeExcluir ? 'pointer-events: none;' : '' }}" @if(!$this->podeExcluir) disabled @endif>
                        <i class="bi bi-trash"></i> Excluir
                    </button>
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;"></th>
                        <th>Relatório</th>
                        <th>Status</th>
                        <th>Data da Solicitação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($relatorios as $relatorio)
                        <tr>
                            <td>
                                <input type="checkbox" wire:model.live="selecionados" value="{{ $relatorio->id }}" class="form-check-input">
                            </td>
                            <td>
                                <div class="fw-semibold">PDF #{{ $relatorio->id }}</div>
                                <div class="small text-secondary text-truncate" style="max-width: 250px;">
                                    @php
                                        $modulos = $relatorio->parametros['modulosSelecionados'] ?? [];
                                    @endphp
                                    {{ implode(', ', array_map('ucfirst', $modulos)) }}
                                </div>
                            </td>
                            <td>
                                @if ($relatorio->trashed())
                                    <span class="badge text-bg-danger">Excluído</span>
                                @elseif ($relatorio->is_arquivado)
                                    <span class="badge text-bg-secondary">Arquivado (Zip)</span>
                                @else
                                    <span class="badge {{ $relatorio->statusBadge() }}">{{ $relatorio->statusLabel() }}</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $relatorio->created_at->format('d/m/Y') }}</div>
                                <div class="small text-secondary">{{ $relatorio->created_at->format('H:i') }}</div>
                            </td>
                            <td>
                                @if (!$relatorio->trashed() && !$relatorio->is_arquivado && $relatorio->concluido())
                                    <a href="{{ route('admin.relatorios.exports.download', $relatorio) }}" class="btn btn-sm btn-outline-primary" title="Baixar">
                                        <i class="bi bi-download"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-secondary">
                                Nenhum relatório encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if ($relatorios->hasPages())
            <div class="card-footer bg-white py-3 border-top">
                {{ $relatorios->links() }}
            </div>
            @endif
        </div>
    </section>
</div>

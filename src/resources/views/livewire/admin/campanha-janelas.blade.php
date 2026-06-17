<div>
    <article class="card shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-4 d-flex justify-content-between align-items-start gap-3">
            <div>
                <h2 class="h5 fw-bold mb-1"><i class="bi bi-calendar-range me-2"></i>Mapa Visual de Ocupacao</h2>
                <p class="text-secondary mb-0 small">Navegue pelas semanas ou dias para monitorar a disponibilidade de vagas da campanha em cada horario de atendimento.</p>
            </div>
            <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMapa" aria-expanded="false" aria-controls="collapseMapa">
                <i class="bi bi-chevron-expand"></i>
            </button>
        </div>
        
        <div class="collapse" id="collapseMapa" wire:ignore>
            <div class="card-body p-4 pt-0">
                <div 
                    class="appointment-calendar"
                    data-admin-calendar-wrapper
                    data-horarios='@json($this->janelas)'
                    data-horario-inicio="{{ substr($campanha->horario_inicio, 0, 5) }}:00"
                    data-horario-fim="{{ substr($campanha->horario_fim, 0, 5) }}:00"
                >
                    <div data-admin-calendario></div>
                </div>
            </div>
            
            <div class="card-footer bg-light border-top-0 py-3 rounded-bottom-3">
                <div class="d-flex flex-wrap gap-3 small text-secondary">
                    <span class="d-flex align-items-center gap-1">
                        <i class="bi bi-square-fill text-success opacity-50"></i> Disponivel
                    </span>
                    <span class="d-flex align-items-center gap-1">
                        <i class="bi bi-square-fill text-warning"></i> Enchendo (75%+)
                    </span>
                    <span class="d-flex align-items-center gap-1">
                        <i class="bi bi-square-fill text-danger"></i> Lotado (100%)
                    </span>
                </div>
            </div>
        </div>
    </article>
</div>

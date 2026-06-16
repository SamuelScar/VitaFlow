<x-layouts.public title="{{ $campanha->titulo }}">
    @php
        $editando = $errors->updateCampanha->any();
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
        $statusAgendamentoLabels = [
            'agendado' => 'Agendados',
            'cancelado' => 'Cancelados',
            'realizado' => 'Realizados',
            'faltou' => 'Faltas',
        ];
        $statusAgendamentoClasses = [
            'agendado' => 'text-bg-primary',
            'cancelado' => 'text-bg-secondary',
            'realizado' => 'text-bg-success',
            'faltou' => 'text-bg-warning',
        ];
        $tiposAlvo = $campanha->tipos_sanguineos_alvo;
        $tiposAlvo = is_array($tiposAlvo) && count($tiposAlvo) > 0
            ? implode(', ', $tiposAlvo)
            : 'Todos';
    @endphp

    <section class="bg-white border-bottom">
        <div class="container py-5">
            <div class="d-flex flex-column flex-lg-row align-items-lg-start justify-content-between gap-3">
                <div>
                    <span class="badge text-bg-light border mb-3">
                        <i class="bi bi-megaphone me-1" aria-hidden="true"></i>
                        Campanha
                    </span>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <h1 class="h2 fw-bold mb-0">{{ $campanha->titulo }}</h1>
                        <span class="badge {{ $statusClasses[$campanha->status] ?? 'text-bg-light' }}">
                            <i class="bi bi-circle-fill me-1" aria-hidden="true"></i>
                            {{ $statusLabels[$campanha->status] ?? $campanha->status }}
                        </span>
                    </div>
                    <p class="text-secondary mb-0">{{ $campanha->descricao }}</p>
                </div>

                <div class="d-grid d-sm-flex flex-sm-wrap justify-content-lg-end gap-2">
                    <a class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-2" href="{{ route('admin.campanhas.index') }}">
                        <i class="bi bi-arrow-left" aria-hidden="true"></i>
                        Campanhas
                    </a>
                    <a
                        class="btn btn-outline-primary d-inline-flex align-items-center justify-content-center gap-2"
                        href="{{ route('admin.agendamentos.index', ['campanha' => $campanha->id]) }}"
                    >
                        <i class="bi bi-calendar-check" aria-hidden="true"></i>
                        Ver agendamentos
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="border rounded-3 p-3 h-100">
                    <span class="text-secondary small">Periodo</span>
                    <strong class="d-block">{{ $campanha->data_inicio->format('d/m/Y') }} ate {{ $campanha->data_fim->format('d/m/Y') }}</strong>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="border rounded-3 p-3 h-100">
                    <span class="text-secondary small">Atendimento</span>
                    <strong class="d-block">{{ substr((string) $campanha->horario_inicio, 0, 5) }} as {{ substr((string) $campanha->horario_fim, 0, 5) }}</strong>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="border rounded-3 p-3 h-100">
                    <span class="text-secondary small">Meta</span>
                    <strong class="d-block">{{ $campanha->meta_bolsas }} bolsas</strong>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="border rounded-3 p-3 h-100">
                    <span class="text-secondary small">Vagas</span>
                    <strong class="d-block">{{ $campanha->agendamentos_por_horario }} por horario</strong>
                </div>
            </div>
        </div>

        <article class="card shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                    <div>
                        <h2 class="h5 fw-bold mb-1">Informacoes da campanha</h2>
                        <p class="text-secondary mb-0">Dados operacionais usados para agendamento e acompanhamento.</p>
                    </div>
                    <span class="badge text-bg-light border align-self-start">
                        Criada por {{ $campanha->criador?->name ?? 'admin removido' }}
                    </span>
                </div>

                <div class="row g-3">
                    <div class="col-md-6 col-xl-4">
                        <div class="border rounded-3 p-3 h-100">
                            <span class="text-secondary small">Local de coleta</span>
                            <strong class="d-block">{{ $campanha->localColeta?->nome ?? 'Local removido' }}</strong>
                            @if ($campanha->localColeta)
                                <span class="text-secondary small d-block">
                                    {{ $campanha->localColeta->cidade }} - {{ $campanha->localColeta->uf }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="border rounded-3 p-3 h-100">
                            <span class="text-secondary small">Tipos sanguineos alvo</span>
                            <strong class="d-block">{{ $tiposAlvo }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="border rounded-3 p-3 h-100">
                            <span class="text-secondary small">Doacoes registradas</span>
                            <strong class="d-block">{{ $doacoesRegistradas }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </article>

        <div class="row g-3 mb-4">
            @foreach ($statusAgendamentoLabels as $status => $label)
                @php
                    $totalStatus = (int) ($resumoAgendamentos[$status] ?? 0);
                @endphp
                <div class="col-6 col-lg-3">
                    <div class="border rounded-3 p-3 h-100">
                        <span class="badge {{ $statusAgendamentoClasses[$status] }} mb-2">{{ $label }}</span>
                        <strong class="fs-4 d-block">{{ $totalStatus }}</strong>
                        <span class="text-secondary small">{{ $totalStatus === 1 ? 'agendamento' : 'agendamentos' }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <article class="card shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                    <div>
                        <h2 class="h5 fw-bold mb-1">Gerenciar campanha</h2>
                        <p class="text-secondary mb-0">Altere dados, periodo, vagas e status da campanha.</p>
                    </div>

                    @if ($totalAgendamentos === 0)
                        <form
                            class="d-grid m-0 align-self-lg-start"
                            method="POST"
                            action="{{ route('admin.campanhas.destroy', $campanha) }}"
                            data-confirm-title="Excluir campanha?"
                            data-confirm-text="Esta acao nao podera ser desfeita."
                            data-confirm-button-text="Excluir"
                            data-confirm-button-color="#c62828"
                            data-confirm-delay-ms="3000"
                        >
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger d-inline-flex align-items-center justify-content-center gap-2" type="submit">
                                <i class="bi bi-trash" aria-hidden="true"></i>
                                Excluir
                            </button>
                        </form>
                    @else
                        <span class="badge text-bg-light border align-self-lg-start">
                            Exclusao bloqueada por agendamentos
                        </span>
                    @endif
                </div>

                @include('admin.campanhas.partials.form', [
                    'action' => route('admin.campanhas.update', $campanha),
                    'method' => 'PUT',
                    'submitLabel' => 'Salvar alteracoes',
                    'idPrefix' => "detalhe_campanha_{$campanha->id}",
                    'campanha' => $campanha,
                    'locaisColeta' => $locaisColeta,
                    'tiposSanguineos' => $tiposSanguineos,
                    'errorBag' => 'updateCampanha',
                    'useOldValues' => $editando,
                    'confirmTitle' => 'Salvar alteracoes da campanha?',
                    'confirmText' => 'As novas informacoes serao aplicadas nesta campanha.',
                    'confirmButtonText' => 'Salvar alteracoes',
                    'confirmButtonColor' => '#0d6efd',
                    'confirmDelayMs' => 3000,
                ])
            </div>
        </article>

        <livewire:admin.agendamento-list :campanha-id="(string) $campanha->id" :campanha-travada="true" />
    </section>
</x-layouts.public>

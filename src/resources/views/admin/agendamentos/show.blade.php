<x-layouts.public title="Detalhes do Agendamento">
    <section class="bg-white border-bottom">
        <div class="container py-5">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <span class="badge text-bg-light border mb-3">
                        <i class="bi bi-shield-check me-1" aria-hidden="true"></i>
                        Administracao
                    </span>
                    <h1 class="h2 fw-bold mb-1">Detalhes do Agendamento</h1>
                    <p class="text-secondary mb-0">Informacoes do doador e do registro de coleta.</p>
                </div>

                <a class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" href="{{ route('admin.agendamentos.index') }}">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    Voltar para lista
                </a>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <div class="row g-4 mb-4">
            <div class="col-12 col-xl-6">
                <article class="card shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h2 class="h5 fw-bold mb-0"><i class="bi bi-person me-2"></i>Dados do Doador</h2>
                    </div>
                    <div class="card-body p-4">
                        @if ($agendamento->user)
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <span class="text-secondary small d-block">Nome completo</span>
                                    <strong class="d-block">{{ $agendamento->user->name }}</strong>
                                </div>
                                <div class="col-12 col-md-6">
                                    <span class="text-secondary small d-block">E-mail</span>
                                    <strong class="d-block">{{ $agendamento->user->email }}</strong>
                                </div>
                                <div class="col-12 col-md-4">
                                    <span class="text-secondary small d-block">CPF</span>
                                    <strong class="d-block">{{ $agendamento->user->cpf ?? 'Nao informado' }}</strong>
                                </div>
                                <div class="col-12 col-md-4">
                                    <span class="text-secondary small d-block">Telefone</span>
                                    <strong class="d-block">{{ $agendamento->user->telefone ?? 'Nao informado' }}</strong>
                                </div>
                                <div class="col-12 col-md-4">
                                    <span class="text-secondary small d-block">Sexo</span>
                                    <strong class="d-block">{{ $agendamento->user->sexo ? ucfirst($agendamento->user->sexo) : 'Nao informado' }}</strong>
                                </div>
                                <div class="col-12 col-md-4">
                                    <span class="text-secondary small d-block">Data de nascimento</span>
                                    <strong class="d-block">{{ $agendamento->user->data_nascimento?->format('d/m/Y') ?? 'Nao informada' }}</strong>
                                </div>
                                <div class="col-12 col-md-4">
                                    <span class="text-secondary small d-block">Tipo sanguineo</span>
                                    <strong class="d-block">
                                        @if($agendamento->user->tipo_sanguineo)
                                            <span class="badge text-bg-danger">{{ $agendamento->user->tipo_sanguineo }}</span>
                                        @else
                                            Nao informado
                                        @endif
                                    </strong>
                                </div>
                                <div class="col-12 col-md-4">
                                    <span class="text-secondary small d-block">Peso</span>
                                    <strong class="d-block">{{ $agendamento->user->peso ? number_format($agendamento->user->peso, 1, ',', '') . ' kg' : 'Nao informado' }}</strong>
                                </div>
                                <div class="col-12">
                                    <hr class="my-2">
                                    <span class="text-secondary small d-block mb-1">Carteirinha de Doador</span>
                                    @if ($agendamento->user->carteiraDoacao && $agendamento->user->carteiraDoacao->status === 'ativa')
                                        <span class="badge text-bg-success">
                                            <i class="bi bi-card-heading me-1"></i>
                                            Ativa (Registro: {{ $agendamento->user->carteiraDoacao->numero_registro }})
                                        </span>
                                    @elseif ($agendamento->user->carteiraDoacao && $agendamento->user->carteiraDoacao->status === 'suspensa')
                                        <span class="badge text-bg-warning">
                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                            Suspensa
                                        </span>
                                    @else
                                        <span class="badge text-bg-secondary">
                                            <i class="bi bi-x-circle me-1"></i>
                                            Nao emitida / Inativa
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="alert alert-secondary mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                O usuario vinculado a este agendamento foi removido do sistema.
                            </div>
                        @endif
                    </div>
                </article>
            </div>

            <div class="col-12 col-xl-6">
                <article class="card shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h2 class="h5 fw-bold mb-0"><i class="bi bi-calendar-check me-2"></i>Dados do Agendamento</h2>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <span class="text-secondary small d-block">Campanha</span>
                                <strong class="d-block">{{ $agendamento->campanha?->titulo ?? 'Campanha indisponivel' }}</strong>
                            </div>
                            <div class="col-12 col-md-6">
                                <span class="text-secondary small d-block">Local da Coleta</span>
                                <strong class="d-block">{{ $agendamento->campanha?->localColeta?->nome ?? 'Local indisponivel' }}</strong>
                            </div>
                            <div class="col-12 col-md-6">
                                <span class="text-secondary small d-block">Data e Hora do Agendamento</span>
                                <strong class="d-block fs-5">
                                    {{ $agendamento->data_hora->format('d/m/Y') }} as {{ $agendamento->data_hora->format('H:i') }}
                                </strong>
                            </div>
                            <div class="col-12 col-md-6">
                                <span class="text-secondary small d-block mb-1">Status Operacional</span>
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
                                @endphp
                                <span class="badge {{ $statusClasses[$agendamento->status] ?? 'text-bg-light' }} fs-6">
                                    {{ $statusLabels[$agendamento->status] ?? ucfirst($agendamento->status) }}
                                </span>
                            </div>

                            <div class="col-12">
                                <hr class="my-2">
                                <span class="text-secondary small d-block mb-2">Resultado da Doacao</span>
                                @if ($agendamento->doacao)
                                    @if ($agendamento->doacao->status === 'confirmada')
                                        <div class="alert alert-success mb-0">
                                            <i class="bi bi-check-circle-fill me-2"></i>
                                            <strong>Doacao confirmada!</strong>
                                            <ul class="mb-0 mt-2">
                                                <li>Data da coleta: {{ $agendamento->doacao->data_coleta->format('d/m/Y H:i') }}</li>
                                                <li>Volume coletado: {{ number_format((int) $agendamento->doacao->quantidade_ml, 0, ',', '.') }} ml</li>
                                                @if ($agendamento->doacao->bolsaSangue)
                                                    <li>Bolsa de sangue gerada: <strong>{{ ucfirst($agendamento->doacao->bolsaSangue->status) }}</strong></li>
                                                @endif
                                            </ul>
                                        </div>
                                    @else
                                        <div class="alert alert-warning mb-0">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                            <strong>Doacao recusada na triagem.</strong>
                                            <p class="mb-0 mt-2 small">
                                                <strong>Motivo clinico:</strong> {{ $agendamento->doacao->motivo_recusa }}
                                            </p>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-secondary">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Ainda nao ha registro de doacao para este agendamento.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>
</x-layouts.public>

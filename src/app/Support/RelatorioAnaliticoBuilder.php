<?php

namespace App\Support;

use App\Models\Agendamento;
use App\Models\BolsaSangue;
use App\Models\Campanha;
use App\Models\Doacao;
use App\Models\EstoqueSangue;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class RelatorioAnaliticoBuilder
{
    private array $modulosSelecionados;
    private string $graficoPrincipal;
    private string $filtroDataInicio;
    private string $filtroDataFim;
    private array $filtroStatusAgendamento;
    private array $filtroStatusBolsa;
    private array $filtroStatusCampanha;
    private array $filtroTipoSanguineo;
    private array $filtroLocalColeta;

    public function __construct(array $parametros, string $graficoPrincipal)
    {
        $this->modulosSelecionados = is_array($parametros['modulosSelecionados'] ?? null)
            ? $parametros['modulosSelecionados']
            : [];
        $this->graficoPrincipal = $graficoPrincipal;
        $this->filtroDataInicio = (string) ($parametros['filtroDataInicio'] ?? '');
        $this->filtroDataFim = (string) ($parametros['filtroDataFim'] ?? '');
        $this->filtroStatusAgendamento = $this->arrayParametro($parametros, 'filtroStatusAgendamento');
        $this->filtroStatusBolsa = $this->arrayParametro($parametros, 'filtroStatusBolsa');
        $this->filtroStatusCampanha = $this->arrayParametro($parametros, 'filtroStatusCampanha');
        $this->filtroTipoSanguineo = $this->arrayParametro($parametros, 'filtroTipoSanguineo');
        $this->filtroLocalColeta = $this->arrayParametro($parametros, 'filtroLocalColeta');
    }

    public function getPainelAnalitico(array $dadosPorModulo): array
    {
        $agendamentos = $dadosPorModulo['agendamentos'] ?? collect();
        $bolsas = $dadosPorModulo['bolsas'] ?? collect();
        $campanhas = $dadosPorModulo['campanhas'] ?? collect();
        $doadores = $dadosPorModulo['doadores'] ?? collect();

        return [
            'cards' => $this->getCardsIndicadores($agendamentos, $bolsas, $campanhas, $doadores),
            'statusAgendamentos' => $this->getGraficoStatusAgendamentos($agendamentos),
            'evolucaoAgendamentos' => $this->getGraficoEvolucaoAgendamentos($agendamentos),
            'campanhas' => $this->getGraficoCampanhas($campanhas),
            'estoque' => $this->getGraficoEstoque($bolsas),
            'doadores' => $this->getGraficoDoadores($doadores),
        ];
    }

    public function getGraficoPrincipal(): array
    {
        return match ($this->graficoPrincipal) {
            'agendamentos_periodo' => $this->graficoAgendamentosPorPeriodo(),
            'comparecimento_periodo' => $this->graficoComparecimentoPorPeriodo(),
            'bolsas_tipo' => $this->graficoBolsasPorTipo(),
            'estoque_tipo' => $this->graficoEstoquePorTipo(),
            'campanhas_desempenho' => $this->graficoCampanhasPorDesempenho(),
            default => $this->graficoDoacoesPorPeriodo(),
        };
    }

    private function graficoDoacoesPorPeriodo(): array
    {
        $linhas = $this->doacoesQuery()
            ->get()
            ->groupBy(fn (Doacao $doacao) => $doacao->data_coleta->format('Y-m-d'))
            ->map(function (Collection $grupo): array {
                $primeiraDoacao = $grupo->first();

                return [
                    'label' => $primeiraDoacao?->data_coleta->format('d/m') ?? '-',
                    'total' => $grupo->count(),
                    'volume' => (int) $grupo->sum('quantidade_ml'),
                ];
            })
            ->values()
            ->take(-14);

        return $this->montarGrafico(
            'Doações confirmadas por período',
            'Quantidade de doações confirmadas e volume coletado.',
            'line',
            $linhas->pluck('label')->all(),
            [
                [
                    'label' => 'Doações',
                    'data' => $linhas->pluck('total')->all(),
                    'borderColor' => '#dc3545',
                    'backgroundColor' => 'rgba(220, 53, 69, 0.12)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Volume (ml)',
                    'data' => $linhas->pluck('volume')->all(),
                    'borderColor' => '#0d6efd',
                    'backgroundColor' => 'rgba(13, 110, 253, 0.12)',
                    'fill' => false,
                    'tension' => 0.35,
                    'yAxisID' => 'volume',
                ],
            ],
            [
                'volume' => [
                    'beginAtZero' => true,
                    'position' => 'right',
                    'grid' => ['drawOnChartArea' => false],
                ],
            ],
        );
    }

    private function graficoAgendamentosPorPeriodo(): array
    {
        $linhas = $this->agendamentosAnaliticosQuery()
            ->get()
            ->groupBy(fn (Agendamento $agendamento) => $agendamento->data_hora->format('Y-m-d'))
            ->map(function (Collection $grupo): array {
                $primeiroAgendamento = $grupo->first();

                return [
                    'label' => $primeiroAgendamento?->data_hora->format('d/m') ?? '-',
                    'total' => $grupo->count(),
                ];
            })
            ->values()
            ->take(-14);

        return $this->montarGrafico(
            'Agendamentos por período',
            'Volume de agendamentos criados para as campanhas filtradas.',
            'bar',
            $linhas->pluck('label')->all(),
            [
                [
                    'label' => 'Agendamentos',
                    'data' => $linhas->pluck('total')->all(),
                    'backgroundColor' => '#dc3545',
                    'borderRadius' => 6,
                ],
            ],
        );
    }

    private function graficoComparecimentoPorPeriodo(): array
    {
        $linhas = $this->agendamentosAnaliticosQuery()
            ->get()
            ->groupBy(fn (Agendamento $agendamento) => $agendamento->data_hora->format('Y-m-d'))
            ->map(function (Collection $grupo): array {
                $primeiroAgendamento = $grupo->first();

                return [
                    'label' => $primeiroAgendamento?->data_hora->format('d/m') ?? '-',
                    'realizados' => $grupo->where('status', Agendamento::STATUS_REALIZADO)->count(),
                    'faltas' => $grupo->where('status', Agendamento::STATUS_FALTOU)->count(),
                    'cancelados' => $grupo->where('status', Agendamento::STATUS_CANCELADO)->count(),
                ];
            })
            ->values()
            ->take(-14);

        return $this->montarGrafico(
            'Comparecimento x faltas',
            'Evolução de presença, faltas e cancelamentos por data.',
            'bar',
            $linhas->pluck('label')->all(),
            [
                [
                    'label' => 'Realizados',
                    'data' => $linhas->pluck('realizados')->all(),
                    'backgroundColor' => '#198754',
                    'borderRadius' => 6,
                ],
                [
                    'label' => 'Faltas',
                    'data' => $linhas->pluck('faltas')->all(),
                    'backgroundColor' => '#ffc107',
                    'borderRadius' => 6,
                ],
                [
                    'label' => 'Cancelados',
                    'data' => $linhas->pluck('cancelados')->all(),
                    'backgroundColor' => '#6c757d',
                    'borderRadius' => 6,
                ],
            ],
        );
    }

    private function graficoBolsasPorTipo(): array
    {
        $totais = $this->bolsasAnaliticasQuery()
            ->get()
            ->groupBy('tipo_sanguineo')
            ->map(fn (Collection $grupo) => [
                'bolsas' => $grupo->count(),
                'volume' => (int) $grupo->sum('quantidade_ml'),
            ]);

        $tipos = collect(TipoSanguineo::values());

        return $this->montarGrafico(
            'Bolsas coletadas por tipo sanguíneo',
            'Quantidade de bolsas e volume total coletado por tipo.',
            'bar',
            $tipos->all(),
            [
                [
                    'label' => 'Bolsas',
                    'data' => $tipos->map(fn (string $tipo) => (int) ($totais[$tipo]['bolsas'] ?? 0))->all(),
                    'backgroundColor' => '#dc3545',
                    'borderRadius' => 6,
                ],
                [
                    'label' => 'Volume (ml)',
                    'data' => $tipos->map(fn (string $tipo) => (int) ($totais[$tipo]['volume'] ?? 0))->all(),
                    'backgroundColor' => '#0d6efd',
                    'borderRadius' => 6,
                    'yAxisID' => 'volume',
                ],
            ],
            [
                'volume' => [
                    'beginAtZero' => true,
                    'position' => 'right',
                    'grid' => ['drawOnChartArea' => false],
                ],
            ],
        );
    }

    private function graficoEstoquePorTipo(): array
    {
        $totais = BolsaSangue::disponiveis()
            ->when(! empty($this->filtroLocalColetaIds()), fn (Builder $query) => $query->whereIn('local_coleta_id', $this->filtroLocalColetaIds()))
            ->when(! empty($this->filtroTiposSanguineos()), fn (Builder $query) => $query->whereIn('tipo_sanguineo', $this->filtroTiposSanguineos()))
            ->get()
            ->groupBy('tipo_sanguineo')
            ->map(fn (Collection $grupo) => [
                'bolsas' => $grupo->count(),
                'volume' => (int) $grupo->sum('quantidade_ml'),
            ]);

        $tipos = collect(TipoSanguineo::values());

        return $this->montarGrafico(
            'Estoque disponível por tipo sanguíneo',
            'Volume em estoque considerando apenas bolsas disponíveis e dentro da validade.',
            'bar',
            $tipos->all(),
            [
                [
                    'label' => 'Volume disponível (ml)',
                    'data' => $tipos->map(fn (string $tipo) => (int) ($totais[$tipo]['volume'] ?? 0))->all(),
                    'backgroundColor' => '#dc3545',
                    'borderRadius' => 6,
                ],
                [
                    'label' => 'Bolsas disponíveis',
                    'data' => $tipos->map(fn (string $tipo) => (int) ($totais[$tipo]['bolsas'] ?? 0))->all(),
                    'backgroundColor' => '#20c997',
                    'borderRadius' => 6,
                    'yAxisID' => 'bolsas',
                ],
            ],
            [
                'bolsas' => [
                    'beginAtZero' => true,
                    'position' => 'right',
                    'grid' => ['drawOnChartArea' => false],
                ],
            ],
        );
    }

    private function graficoCampanhasPorDesempenho(): array
    {
        $campanhas = $this->campanhasAnaliticasQuery()
            ->get()
            ->map(function (Campanha $campanha): array {
                $doacoesConfirmadas = $campanha->agendamentos
                    ->filter(fn (Agendamento $agendamento) => $agendamento->doacao?->status === 'confirmada')
                    ->count();

                return [
                    'titulo' => $campanha->titulo,
                    'doacoes' => $doacoesConfirmadas,
                    'meta' => (int) $campanha->meta_bolsas,
                ];
            })
            ->sortByDesc('doacoes')
            ->take(8)
            ->values();

        return $this->montarGrafico(
            'Campanhas por desempenho',
            'Comparação entre doações confirmadas e meta de bolsas.',
            'bar',
            $campanhas->pluck('titulo')->all(),
            [
                [
                    'label' => 'Doações confirmadas',
                    'data' => $campanhas->pluck('doacoes')->all(),
                    'backgroundColor' => '#198754',
                    'borderRadius' => 6,
                ],
                [
                    'label' => 'Meta de bolsas',
                    'data' => $campanhas->pluck('meta')->all(),
                    'backgroundColor' => '#dc3545',
                    'borderRadius' => 6,
                ],
            ],
        );
    }

    private function montarGrafico(string $titulo, string $descricao, string $tipo, array $labels, array $datasets, array $escalasExtras = []): array
    {
        $chart = [
            'type' => $tipo,
            'data' => [
                'labels' => $labels,
                'datasets' => $datasets,
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'position' => 'bottom',
                    ],
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                    ],
                    ...$escalasExtras,
                ],
            ],
        ];

        return [
            'titulo' => $titulo,
            'descricao' => $descricao,
            'vazio' => empty($labels) || collect($datasets)->every(fn (array $dataset) => collect($dataset['data'] ?? [])->sum() === 0),
            'chart' => $chart,
            'key' => md5(json_encode($chart)),
        ];
    }

    private function agendamentosAnaliticosQuery(): Builder
    {
        return Agendamento::query()
            ->with(['doacao', 'campanha.localColeta', 'user'])
            ->when($this->filtroDataInicio !== '', fn (Builder $query) => $query->whereDate('data_hora', '>=', $this->filtroDataInicio))
            ->when($this->filtroDataFim !== '', fn (Builder $query) => $query->whereDate('data_hora', '<=', $this->filtroDataFim))
            ->when(! empty($this->filtroStatusAgendamento), fn (Builder $query) => $query->whereIn('status', $this->filtroStatusAgendamento))
            ->when(! empty($this->filtroLocalColetaIds()), function (Builder $query): void {
                $query->whereHas('campanha', fn (Builder $query) => $query->whereIn('local_coleta_id', $this->filtroLocalColetaIds()));
            })
            ->orderBy('data_hora');
    }

    private function doacoesQuery(): Builder
    {
        return Doacao::query()
            ->with(['agendamento.campanha.localColeta', 'agendamento.user'])
            ->where('status', 'confirmada')
            ->when($this->filtroDataInicio !== '', fn (Builder $query) => $query->whereDate('data_coleta', '>=', $this->filtroDataInicio))
            ->when($this->filtroDataFim !== '', fn (Builder $query) => $query->whereDate('data_coleta', '<=', $this->filtroDataFim))
            ->when(! empty($this->filtroLocalColetaIds()), function (Builder $query): void {
                $query->whereHas('agendamento.campanha', fn (Builder $query) => $query->whereIn('local_coleta_id', $this->filtroLocalColetaIds()));
            })
            ->when(! empty($this->filtroTiposSanguineos()), function (Builder $query): void {
                $query->whereHas('agendamento.user', fn (Builder $query) => $query->whereIn('tipo_sanguineo', $this->filtroTiposSanguineos()));
            })
            ->when(! empty($this->filtroStatusAgendamento), function (Builder $query): void {
                $query->whereHas('agendamento', fn (Builder $query) => $query->whereIn('status', $this->filtroStatusAgendamento));
            })
            ->orderBy('data_coleta');
    }

    private function bolsasAnaliticasQuery(): Builder
    {
        return BolsaSangue::query()
            ->when($this->filtroDataInicio !== '', fn (Builder $query) => $query->whereDate('data_coleta', '>=', $this->filtroDataInicio))
            ->when($this->filtroDataFim !== '', fn (Builder $query) => $query->whereDate('data_coleta', '<=', $this->filtroDataFim))
            ->when(! empty($this->filtroLocalColetaIds()), fn (Builder $query) => $query->whereIn('local_coleta_id', $this->filtroLocalColetaIds()))
            ->when(! empty($this->filtroTiposSanguineos()), fn (Builder $query) => $query->whereIn('tipo_sanguineo', $this->filtroTiposSanguineos()))
            ->when(! empty($this->filtroStatusBolsa), fn (Builder $query) => $query->whereIn('status', $this->filtroStatusBolsa));
    }

    private function campanhasAnaliticasQuery(): Builder
    {
        return Campanha::query()
            ->with(['agendamentos.doacao'])
            ->when($this->filtroDataInicio !== '', fn (Builder $query) => $query->whereDate('data_inicio', '>=', $this->filtroDataInicio))
            ->when($this->filtroDataFim !== '', fn (Builder $query) => $query->whereDate('data_fim', '<=', $this->filtroDataFim))
            ->when(! empty($this->filtroStatusCampanha), fn (Builder $query) => $query->whereIn('status', $this->filtroStatusCampanha))
            ->when(! empty($this->filtroLocalColetaIds()), fn (Builder $query) => $query->whereIn('local_coleta_id', $this->filtroLocalColetaIds()));
    }

    private function getCardsIndicadores(Collection $agendamentos, Collection $bolsas, Collection $campanhas, Collection $doadores): array
    {
        $cards = [];

        if (in_array('agendamentos', $this->modulosSelecionados, true)) {
            $totalAgendamentos = $agendamentos->count();
            $totalRealizados = $agendamentos->where('status', Agendamento::STATUS_REALIZADO)->count();
            $doacoesConfirmadas = $agendamentos
                ->filter(fn (Agendamento $agendamento) => $agendamento->doacao?->status === 'confirmada')
                ->count();

            $cards[] = [
                'titulo' => 'Agendamentos',
                'valor' => number_format($totalAgendamentos, 0, ',', '.'),
                'detalhe' => 'registros no filtro',
                'icone' => 'bi-calendar-check',
            ];
            $cards[] = [
                'titulo' => 'Comparecimento',
                'valor' => $this->percentual($totalRealizados, $totalAgendamentos) . '%',
                'detalhe' => "{$totalRealizados} realizados",
                'icone' => 'bi-person-check',
            ];
            $cards[] = [
                'titulo' => 'Conversão em doação',
                'valor' => $this->percentual($doacoesConfirmadas, $totalAgendamentos) . '%',
                'detalhe' => "{$doacoesConfirmadas} doações confirmadas",
                'icone' => 'bi-droplet',
            ];
        }

        if (in_array('bolsas', $this->modulosSelecionados, true)) {
            $bolsasDisponiveis = $bolsas->filter(fn (BolsaSangue $bolsa) => $bolsa->estaDisponivel());
            $volumeDisponivel = $bolsasDisponiveis->sum('quantidade_ml');

            $cards[] = [
                'titulo' => 'Estoque disponível',
                'valor' => number_format($volumeDisponivel, 0, ',', '.') . ' ml',
                'detalhe' => $bolsasDisponiveis->count() . ' bolsas disponíveis',
                'icone' => 'bi-box-seam',
            ];
            $cards[] = [
                'titulo' => 'Estoques críticos',
                'valor' => (string) $this->contarEstoquesCriticos(),
                'detalhe' => 'abaixo do mínimo configurado',
                'icone' => 'bi-exclamation-triangle',
            ];
        }

        if (in_array('campanhas', $this->modulosSelecionados, true)) {
            $cards[] = [
                'titulo' => 'Campanhas ativas',
                'valor' => (string) $campanhas->where('status', 'ativa')->count(),
                'detalhe' => $campanhas->count() . ' campanhas no filtro',
                'icone' => 'bi-megaphone',
            ];
        }

        if (in_array('doadores', $this->modulosSelecionados, true)) {
            $cards[] = [
                'titulo' => 'Doadores',
                'valor' => number_format($doadores->count(), 0, ',', '.'),
                'detalhe' => 'cadastros no filtro',
                'icone' => 'bi-people',
            ];
        }

        return $cards;
    }

    private function getGraficoStatusAgendamentos(Collection $agendamentos): array
    {
        if (! in_array('agendamentos', $this->modulosSelecionados, true)) {
            return [];
        }

        $totais = $agendamentos->groupBy('status')->map->count();
        $maiorTotal = max(1, (int) $totais->max());

        return collect($this->getStatusAgendamento())
            ->map(function (string $label, string $status) use ($totais, $maiorTotal): array {
                $total = (int) ($totais[$status] ?? 0);

                return [
                    'label' => $label,
                    'total' => $total,
                    'percentual' => $this->percentual($total, $maiorTotal),
                    'classe' => match ($status) {
                        Agendamento::STATUS_REALIZADO => 'bg-success',
                        Agendamento::STATUS_FALTOU => 'bg-warning',
                        Agendamento::STATUS_CANCELADO => 'bg-secondary',
                        default => 'bg-primary',
                    },
                ];
            })
            ->values()
            ->all();
    }

    private function getGraficoEvolucaoAgendamentos(Collection $agendamentos): array
    {
        if (! in_array('agendamentos', $this->modulosSelecionados, true)) {
            return [];
        }

        $linhas = $agendamentos
            ->sortBy('data_hora')
            ->groupBy(fn (Agendamento $agendamento) => $agendamento->data_hora->format('Y-m-d'))
            ->map(function (Collection $grupo): array {
                $primeiro = $grupo->first();
                $doacoesConfirmadas = $grupo
                    ->filter(fn (Agendamento $agendamento) => $agendamento->doacao?->status === 'confirmada')
                    ->count();

                return [
                    'label' => $primeiro?->data_hora->format('d/m') ?? '-',
                    'agendamentos' => $grupo->count(),
                    'doacoes' => $doacoesConfirmadas,
                ];
            })
            ->values();

        if ($linhas->count() > 14) {
            $linhas = $linhas->slice(-14)->values();
        }

        $maiorTotal = max(1, (int) $linhas->max('agendamentos'));

        return $linhas
            ->map(fn (array $linha): array => [
                ...$linha,
                'percentual' => $this->percentual((int) $linha['agendamentos'], $maiorTotal),
            ])
            ->all();
    }

    private function getGraficoCampanhas(Collection $campanhas): array
    {
        if (! in_array('campanhas', $this->modulosSelecionados, true)) {
            return [];
        }

        return $campanhas
            ->map(function (Campanha $campanha): array {
                $agendamentos = $campanha->agendamentos;
                $doacoesConfirmadas = $agendamentos
                    ->filter(fn (Agendamento $agendamento) => $agendamento->doacao?->status === 'confirmada')
                    ->count();
                $meta = max(1, (int) $campanha->meta_bolsas);

                return [
                    'titulo' => $campanha->titulo,
                    'agendamentos' => $agendamentos->count(),
                    'doacoes' => $doacoesConfirmadas,
                    'meta' => (int) $campanha->meta_bolsas,
                    'percentual' => min(100, $this->percentual($doacoesConfirmadas, $meta)),
                ];
            })
            ->sortByDesc('doacoes')
            ->take(6)
            ->values()
            ->all();
    }

    private function getGraficoEstoque(Collection $bolsas): array
    {
        if (! in_array('bolsas', $this->modulosSelecionados, true)) {
            return [];
        }

        $totais = $bolsas
            ->filter(fn (BolsaSangue $bolsa) => $bolsa->estaDisponivel())
            ->groupBy('tipo_sanguineo')
            ->map(fn (Collection $grupo) => [
                'bolsas' => $grupo->count(),
                'ml' => (int) $grupo->sum('quantidade_ml'),
            ]);

        $maiorVolume = max(1, (int) $totais->max('ml'));

        return collect(TipoSanguineo::values())
            ->map(function (string $tipo) use ($totais, $maiorVolume): array {
                $dados = $totais[$tipo] ?? ['bolsas' => 0, 'ml' => 0];

                return [
                    'tipo' => $tipo,
                    'bolsas' => (int) $dados['bolsas'],
                    'ml' => (int) $dados['ml'],
                    'percentual' => $this->percentual((int) $dados['ml'], $maiorVolume),
                ];
            })
            ->all();
    }

    private function getGraficoDoadores(Collection $doadores): array
    {
        if (! in_array('doadores', $this->modulosSelecionados, true)) {
            return [];
        }

        $totais = $doadores
            ->groupBy(fn (User $user) => $user->tipo_sanguineo ?: 'Não informado')
            ->map->count();
        $maiorTotal = max(1, (int) $totais->max());

        return collect([...TipoSanguineo::values(), 'Não informado'])
            ->map(function (string $tipo) use ($totais, $maiorTotal): array {
                $total = (int) ($totais[$tipo] ?? 0);

                return [
                    'tipo' => $tipo,
                    'total' => $total,
                    'percentual' => $this->percentual($total, $maiorTotal),
                ];
            })
            ->all();
    }

    private function contarEstoquesCriticos(): int
    {
        $localIds = $this->filtroLocalColetaIds();
        $tipos = $this->filtroTiposSanguineos();

        $estoques = EstoqueSangue::query()
            ->when(! empty($localIds), fn (Builder $query) => $query->whereIn('local_coleta_id', $localIds))
            ->when(! empty($tipos), fn (Builder $query) => $query->whereIn('tipo_sanguineo', $tipos))
            ->get();

        if ($estoques->isEmpty()) {
            return 0;
        }

        $volumesDisponiveis = BolsaSangue::disponiveis()
            ->selectRaw('local_coleta_id, tipo_sanguineo, sum(quantidade_ml) as total_ml')
            ->when(! empty($localIds), fn (Builder $query) => $query->whereIn('local_coleta_id', $localIds))
            ->when(! empty($tipos), fn (Builder $query) => $query->whereIn('tipo_sanguineo', $tipos))
            ->groupBy('local_coleta_id', 'tipo_sanguineo')
            ->get()
            ->keyBy(fn (BolsaSangue $bolsa) => "{$bolsa->local_coleta_id}|{$bolsa->tipo_sanguineo}");

        return $estoques
            ->filter(function (EstoqueSangue $estoque) use ($volumesDisponiveis): bool {
                $volumeAtual = (int) ($volumesDisponiveis->get("{$estoque->local_coleta_id}|{$estoque->tipo_sanguineo}")?->total_ml ?? 0);

                return $volumeAtual < $estoque->estoque_minimo_ml;
            })
            ->count();
    }

    private function filtroLocalColetaIds(): array
    {
        return collect($this->filtroLocalColeta)
            ->filter(fn (mixed $id) => ctype_digit((string) $id))
            ->map(fn (mixed $id) => (int) $id)
            ->values()
            ->all();
    }

    private function filtroTiposSanguineos(): array
    {
        return collect($this->filtroTipoSanguineo)
            ->filter(fn (mixed $tipo) => in_array($tipo, TipoSanguineo::values(), true))
            ->values()
            ->all();
    }

    private function percentual(int $valor, int $total): int
    {
        if ($total <= 0) {
            return 0;
        }

        return (int) round(($valor / $total) * 100);
    }

    private function getStatusAgendamento(): array
    {
        return [
            Agendamento::STATUS_AGENDADO => 'Agendado',
            Agendamento::STATUS_REALIZADO => 'Realizado',
            Agendamento::STATUS_FALTOU => 'Faltou',
            Agendamento::STATUS_CANCELADO => 'Cancelado',
        ];
    }

    private function arrayParametro(array $parametros, string $chave): array
    {
        return is_array($parametros[$chave] ?? null) ? $parametros[$chave] : [];
    }
}

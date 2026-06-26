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
use Symfony\Component\Process\Process;
use Throwable;

class RelatorioDadosBuilder
{
    public array $modulosSelecionados;
    public array $colunasSelecionadas;

    private string $filtroDataInicio;
    private string $filtroDataFim;
    private array $filtroStatusAgendamento;
    private array $filtroStatusBolsa;
    private array $filtroStatusCampanha;
    private array $filtroTipoSanguineo;
    private array $filtroLocalColeta;
    private array $graficosSelecionados;
    private bool $incluirIndicadores;

    public function __construct(array $parametros = [])
    {
        $this->modulosSelecionados = $this->normalizarLista(
            $parametros['modulosSelecionados'] ?? [],
            array_keys(self::modulos())
        );

        $this->colunasSelecionadas = $this->normalizarColunas($parametros['colunasSelecionadas'] ?? []);
        $this->filtroDataInicio = (string) ($parametros['filtroDataInicio'] ?? '');
        $this->filtroDataFim = (string) ($parametros['filtroDataFim'] ?? '');
        $this->filtroStatusAgendamento = $this->normalizarLista(
            $parametros['filtroStatusAgendamento'] ?? [],
            array_keys($this->getStatusAgendamento())
        );
        $this->filtroStatusBolsa = $this->normalizarLista(
            $parametros['filtroStatusBolsa'] ?? [],
            array_keys($this->getStatusBolsa())
        );
        $this->filtroStatusCampanha = $this->normalizarLista(
            $parametros['filtroStatusCampanha'] ?? [],
            array_keys($this->getStatusCampanha())
        );
        $this->filtroTipoSanguineo = $this->normalizarLista(
            $parametros['filtroTipoSanguineo'] ?? [],
            TipoSanguineo::values()
        );
        $this->filtroLocalColeta = is_array($parametros['filtroLocalColeta'] ?? null)
            ? $parametros['filtroLocalColeta']
            : [];
        $this->graficosSelecionados = $this->normalizarLista(
            $parametros['graficosSelecionados'] ?? [],
            array_keys(self::graficosPrincipais())
        );
        $this->incluirIndicadores = (bool) ($parametros['incluirIndicadores'] ?? true);
    }

    public static function modulos(): array
    {
        return [
            'agendamentos' => 'Agendamentos e Comparecimentos',
            'bolsas' => 'Bolsas de Sangue (Estoque)',
            'campanhas' => 'Campanhas de Doação',
            'doadores' => 'Doadores Cadastrados',
        ];
    }

    public static function opcoesColunas(string $modulo): array
    {
        return match ($modulo) {
            'agendamentos' => [
                'id' => 'ID',
                'doador' => 'Doador',
                'data_hora' => 'Data / Hora',
                'campanha' => 'Campanha',
                'local' => 'Local de Coleta',
                'status' => 'Status',
            ],
            'bolsas' => [
                'id' => 'ID da Bolsa',
                'tipo_sanguineo' => 'Tipo Sanguíneo',
                'local' => 'Local Atual',
                'quantidade' => 'Qtd (ml)',
                'data_coleta' => 'Data de Coleta',
                'validade' => 'Validade',
                'status' => 'Status',
            ],
            'campanhas' => [
                'id' => 'ID',
                'titulo' => 'Título',
                'local' => 'Local',
                'status' => 'Status',
                'periodo' => 'Período',
                'meta' => 'Meta (bolsas)',
            ],
            'doadores' => [
                'id' => 'ID',
                'nome' => 'Nome',
                'email' => 'E-mail',
                'tipo_sanguineo' => 'Tipo Sanguíneo',
            ],
            default => [],
        };
    }

    public static function graficosPrincipais(): array
    {
        return [
            'doacoes_periodo' => 'Doações confirmadas por período',
            'agendamentos_periodo' => 'Agendamentos por período',
            'comparecimento_periodo' => 'Comparecimento x faltas',
            'bolsas_tipo' => 'Bolsas coletadas por tipo sanguíneo',
            'estoque_tipo' => 'Estoque disponível por tipo sanguíneo',
            'campanhas_desempenho' => 'Campanhas por desempenho',
            'ranking_doadores_volume' => 'Ranking de doadores por volume',
            'eficiencia_campanhas_locais' => 'Eficiência de campanhas por local',
        ];
    }

    public function getModulos(): array
    {
        return self::modulos();
    }

    public function getOpcoesColunas(string $modulo): array
    {
        return self::opcoesColunas($modulo);
    }

    public function getDados(): array
    {
        $dadosPorModulo = [];

        foreach ($this->modulosSelecionados as $modulo) {
            $dadosPorModulo[$modulo] = $this->getQuery($modulo)->get();
        }

        return $dadosPorModulo;
    }

    public function getPainelAnalitico(array $dadosPorModulo): array
    {
        $agendamentos = $dadosPorModulo['agendamentos'] ?? collect();
        $bolsas = $dadosPorModulo['bolsas'] ?? collect();
        $campanhas = $dadosPorModulo['campanhas'] ?? collect();
        $doadores = $dadosPorModulo['doadores'] ?? collect();

        return [
            'cards' => $this->incluirIndicadores
                ? $this->getCardsIndicadores($agendamentos, $bolsas, $campanhas, $doadores)
                : [],
        ];
    }

    public function getGraficosSelecionados(): array
    {
        return collect($this->graficosSelecionados)
            ->map(fn (string $grafico) => $this->getGrafico($grafico))
            ->reject(fn (array $grafico) => $grafico['vazio'])
            ->values()
            ->all();
    }

    public function formatarValor($modelo, string $modulo, string $coluna): string
    {
        if ($modulo === 'agendamentos') {
            return match ($coluna) {
                'id' => (string) $modelo->id,
                'doador' => $modelo->user?->name ?? '-',
                'data_hora' => $modelo->data_hora ? $modelo->data_hora->format('d/m/Y H:i') : '-',
                'campanha' => $modelo->campanha?->titulo ?? '-',
                'local' => $modelo->campanha?->localColeta?->nome ?? '-',
                'status' => ucfirst($modelo->status),
                default => '-',
            };
        }

        if ($modulo === 'bolsas') {
            return match ($coluna) {
                'id' => (string) $modelo->id,
                'tipo_sanguineo' => $modelo->tipo_sanguineo ?? '-',
                'local' => $modelo->localColeta?->nome ?? 'Desconhecido',
                'quantidade' => (string) $modelo->quantidade_ml,
                'data_coleta' => $modelo->data_coleta ? $modelo->data_coleta->format('d/m/Y') : '-',
                'validade' => $modelo->validade_em ? $modelo->validade_em->format('d/m/Y') : '-',
                'status' => ucfirst($modelo->status),
                default => '-',
            };
        }

        if ($modulo === 'campanhas') {
            return match ($coluna) {
                'id' => (string) $modelo->id,
                'titulo' => $modelo->titulo ?? '-',
                'local' => $modelo->localColeta?->nome ?? '-',
                'status' => ucfirst($modelo->status),
                'periodo' => ($modelo->data_inicio ? $modelo->data_inicio->format('d/m/Y') : '') . ' até ' . ($modelo->data_fim ? $modelo->data_fim->format('d/m/Y') : ''),
                'meta' => (string) $modelo->meta_bolsas,
                default => '-',
            };
        }

        if ($modulo === 'doadores') {
            return match ($coluna) {
                'id' => (string) $modelo->id,
                'nome' => $modelo->name ?? '-',
                'email' => $modelo->email ?? '-',
                'tipo_sanguineo' => $modelo->tipo_sanguineo ?? 'Não informado',
                default => '-',
            };
        }

        return '-';
    }

    private function getGrafico(string $grafico): array
    {
        return match ($grafico) {
            'agendamentos_periodo' => $this->graficoAgendamentosPorPeriodo(),
            'comparecimento_periodo' => $this->graficoComparecimentoPorPeriodo(),
            'bolsas_tipo' => $this->graficoBolsasPorTipo(),
            'estoque_tipo' => $this->graficoEstoquePorTipo(),
            'campanhas_desempenho' => $this->graficoCampanhasPorDesempenho(),
            'ranking_doadores_volume' => $this->graficoRankingDoadoresPorVolume(),
            'eficiencia_campanhas_locais' => $this->graficoEficienciaCampanhasPorLocal(),
            default => $this->graficoDoacoesPorPeriodo(),
        };
    }

    private function graficoDoacoesPorPeriodo(): array
    {
        $linhas = $this->doacoesQuery()
            ->withoutEagerLoads()
            ->reorder()
            ->selectRaw('DATE(data_coleta) as data_referencia, COUNT(*) as total, COALESCE(SUM(quantidade_ml), 0) as volume')
            ->groupByRaw('DATE(data_coleta)')
            ->orderByRaw('DATE(data_coleta)')
            ->get()
            ->take(-14)
            ->map(fn (Doacao $doacao): array => [
                'label' => date('d/m', strtotime((string) $doacao->data_referencia)),
                'total' => (int) $doacao->total,
                'volume' => (int) $doacao->volume,
            ])
            ->values();

        return $this->montarGraficoPdf(
            'Doações confirmadas por período',
            'Quantidade de doações confirmadas e volume coletado.',
            $linhas->pluck('label')->all(),
            [
                ['label' => 'Doações', 'data' => $linhas->pluck('total')->all(), 'color' => '#dc3545'],
                ['label' => 'Volume (ml)', 'data' => $linhas->pluck('volume')->all(), 'color' => '#0d6efd'],
            ],
            'line',
        );
    }

    private function graficoAgendamentosPorPeriodo(): array
    {
        $linhas = $this->agendamentosAnaliticosQuery()
            ->withoutEagerLoads()
            ->reorder()
            ->selectRaw('DATE(data_hora) as data_referencia, COUNT(*) as total')
            ->groupByRaw('DATE(data_hora)')
            ->orderByRaw('DATE(data_hora)')
            ->get()
            ->take(-14)
            ->map(fn (Agendamento $agendamento): array => [
                'label' => date('d/m', strtotime((string) $agendamento->data_referencia)),
                'total' => (int) $agendamento->total,
            ])
            ->values();

        return $this->montarGraficoPdf(
            'Agendamentos por período',
            'Volume de agendamentos criados para as campanhas filtradas.',
            $linhas->pluck('label')->all(),
            [
                ['label' => 'Agendamentos', 'data' => $linhas->pluck('total')->all(), 'color' => '#dc3545'],
            ],
            'line',
        );
    }

    private function graficoComparecimentoPorPeriodo(): array
    {
        $linhas = $this->agendamentosAnaliticosQuery()
            ->withoutEagerLoads()
            ->reorder()
            ->selectRaw(
                "DATE(data_hora) as data_referencia,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as realizados,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as faltas,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as cancelados",
                [
                    Agendamento::STATUS_REALIZADO,
                    Agendamento::STATUS_FALTOU,
                    Agendamento::STATUS_CANCELADO,
                ]
            )
            ->groupByRaw('DATE(data_hora)')
            ->orderByRaw('DATE(data_hora)')
            ->get()
            ->take(-14)
            ->map(fn (Agendamento $agendamento): array => [
                'label' => date('d/m', strtotime((string) $agendamento->data_referencia)),
                'realizados' => (int) $agendamento->realizados,
                'faltas' => (int) $agendamento->faltas,
                'cancelados' => (int) $agendamento->cancelados,
            ])
            ->values();

        return $this->montarGraficoPdf(
            'Comparecimento x faltas',
            'Evolução de presença, faltas e cancelamentos por data.',
            $linhas->pluck('label')->all(),
            [
                ['label' => 'Realizados', 'data' => $linhas->pluck('realizados')->all(), 'color' => '#198754'],
                ['label' => 'Faltas', 'data' => $linhas->pluck('faltas')->all(), 'color' => '#ffc107'],
                ['label' => 'Cancelados', 'data' => $linhas->pluck('cancelados')->all(), 'color' => '#6c757d'],
            ],
        );
    }

    private function graficoBolsasPorTipo(): array
    {
        $totais = $this->bolsasAnaliticasQuery()
            ->selectRaw('tipo_sanguineo, COUNT(*) as bolsas, COALESCE(SUM(quantidade_ml), 0) as volume')
            ->groupBy('tipo_sanguineo')
            ->get()
            ->keyBy('tipo_sanguineo');

        $tipos = collect(TipoSanguineo::values());

        return $this->montarGraficoPdf(
            'Bolsas coletadas por tipo sanguíneo',
            'Quantidade de bolsas e volume total coletado por tipo.',
            $tipos->all(),
            [
                ['label' => 'Bolsas', 'data' => $tipos->map(fn (string $tipo) => (int) ($totais[$tipo]['bolsas'] ?? 0))->all(), 'color' => '#dc3545'],
                ['label' => 'Volume (ml)', 'data' => $tipos->map(fn (string $tipo) => (int) ($totais[$tipo]['volume'] ?? 0))->all(), 'color' => '#0d6efd'],
            ],
        );
    }

    private function graficoEstoquePorTipo(): array
    {
        $totais = BolsaSangue::disponiveis()
            ->when(! empty($this->filtroLocalColetaIds()), fn (Builder $query) => $query->whereIn('local_coleta_id', $this->filtroLocalColetaIds()))
            ->when(! empty($this->filtroTipoSanguineo), fn (Builder $query) => $query->whereIn('tipo_sanguineo', $this->filtroTipoSanguineo))
            ->selectRaw('tipo_sanguineo, COUNT(*) as bolsas, COALESCE(SUM(quantidade_ml), 0) as volume')
            ->groupBy('tipo_sanguineo')
            ->get()
            ->keyBy('tipo_sanguineo');

        $tipos = collect(TipoSanguineo::values());

        return $this->montarGraficoPdf(
            'Estoque disponível por tipo sanguíneo',
            'Volume em estoque considerando apenas bolsas disponíveis e dentro da validade.',
            $tipos->all(),
            [
                ['label' => 'Volume disponível (ml)', 'data' => $tipos->map(fn (string $tipo) => (int) ($totais[$tipo]['volume'] ?? 0))->all(), 'color' => '#dc3545'],
                ['label' => 'Bolsas disponíveis', 'data' => $tipos->map(fn (string $tipo) => (int) ($totais[$tipo]['bolsas'] ?? 0))->all(), 'color' => '#20c997'],
            ],
            'donut',
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

        return $this->montarGraficoPdf(
            'Campanhas por desempenho',
            'Comparação entre doações confirmadas e meta de bolsas.',
            $campanhas->pluck('titulo')->all(),
            [
                ['label' => 'Doações confirmadas', 'data' => $campanhas->pluck('doacoes')->all(), 'color' => '#198754'],
                ['label' => 'Meta de bolsas', 'data' => $campanhas->pluck('meta')->all(), 'color' => '#dc3545'],
            ],
        );
    }

    private function graficoRankingDoadoresPorVolume(): array
    {
        $doadores = $this->doacoesQuery()
            ->withoutEagerLoads()
            ->reorder()
            ->join('agendamentos', 'agendamentos.id', '=', 'doacoes.agendamento_id')
            ->join('users', 'users.id', '=', 'agendamentos.user_id')
            ->where('users.tipo', User::TIPO_DOADOR)
            ->selectRaw(
                'users.name as nome,
                users.tipo_sanguineo as tipo_sanguineo,
                COUNT(doacoes.id) as total_doacoes,
                COALESCE(SUM(doacoes.quantidade_ml), 0) as volume'
            )
            ->groupBy('users.id', 'users.name', 'users.tipo_sanguineo')
            ->orderByDesc('volume')
            ->limit(8)
            ->get()
            ->map(function (Doacao $doacao): array {
                $tipoSanguineo = $doacao->tipo_sanguineo ?: 'Não informado';

                return [
                    'label' => $this->rotuloDoador($doacao->nome, $tipoSanguineo),
                    'doacoes' => (int) $doacao->total_doacoes,
                    'volume' => (int) $doacao->volume,
                ];
            });

        return $this->montarGraficoPdf(
            'Ranking de doadores por volume',
            'Doadores com maior volume total em doações confirmadas.',
            $doadores->pluck('label')->all(),
            [
                ['label' => 'Volume (ml)', 'data' => $doadores->pluck('volume')->all(), 'color' => '#dc3545'],
            ],
            'horizontal_bar',
        );
    }

    private function graficoEficienciaCampanhasPorLocal(): array
    {
        $campanhas = $this->campanhasAnaliticasQuery()
            ->withoutEagerLoads()
            ->reorder()
            ->leftJoin('locais_coleta', 'locais_coleta.id', '=', 'campanhas.local_coleta_id')
            ->leftJoin('agendamentos', 'agendamentos.campanha_id', '=', 'campanhas.id')
            ->leftJoin('doacoes', 'doacoes.agendamento_id', '=', 'agendamentos.id')
            ->when(! empty($this->filtroStatusAgendamento), fn (Builder $query) => $query->whereIn('agendamentos.status', $this->filtroStatusAgendamento))
            ->selectRaw(
                "campanhas.id as id,
                campanhas.titulo as titulo,
                locais_coleta.nome as local,
                COUNT(agendamentos.id) as total_agendamentos,
                SUM(CASE WHEN doacoes.status = ? THEN 1 ELSE 0 END) as doacoes_confirmadas",
                ['confirmada']
            )
            ->groupBy('campanhas.id', 'campanhas.titulo', 'locais_coleta.nome')
            ->get()
            ->map(function (Campanha $campanha): array {
                $agendamentos = (int) $campanha->total_agendamentos;
                $doacoes = (int) $campanha->doacoes_confirmadas;

                return [
                    'label' => $this->rotuloCampanhaLocal($campanha),
                    'agendamentos' => $agendamentos,
                    'doacoes' => $doacoes,
                    'conversao' => $this->percentual($doacoes, $agendamentos),
                ];
            })
            ->sortByDesc('conversao')
            ->take(6)
            ->values();

        return $this->montarGraficoPdf(
            'Eficiência de campanhas por local',
            'Taxa de conversão de agendamentos em doações confirmadas por campanha e local.',
            $campanhas->pluck('label')->all(),
            [
                ['label' => 'Taxa de conversão (%)', 'data' => $campanhas->pluck('conversao')->all(), 'color' => '#198754'],
            ],
            'horizontal_bar',
        );
    }

    private function rotuloDoador(?string $nome, string $tipoSanguineo): string
    {
        return $this->limitarRotulo(($nome ?: 'Doador não identificado') . " ({$tipoSanguineo})", 42);
    }

    private function rotuloCampanhaLocal(Campanha $campanha): string
    {
        $local = $campanha->local ?: ($campanha->localColeta?->nome ?? 'Sem local');

        return $this->limitarRotulo('Campanha #' . $campanha->id . ' - ' . $local, 46);
    }

    private function limitarRotulo(string $rotulo, int $limite): string
    {
        return mb_strlen($rotulo) <= $limite ? $rotulo : mb_substr($rotulo, 0, $limite - 3) . '...';
    }

    private function montarGraficoPdf(string $titulo, string $descricao, array $labels, array $datasets, string $tipo = 'bar'): array
    {
        $vazio = empty($labels) || collect($datasets)->every(fn (array $dataset) => collect($dataset['data'] ?? [])->sum() === 0);

        return [
            'titulo' => $titulo,
            'descricao' => $descricao,
            'labels' => $labels,
            'datasets' => $datasets,
            'tipo' => $tipo,
            'imagem' => $vazio ? null : $this->gerarImagemGrafico($labels, $datasets, $tipo),
            'vazio' => $vazio,
        ];
    }

    private function gerarImagemGrafico(array $labels, array $datasets, string $tipo): ?string
    {
        try {
            $payload = [
                'width' => 1000,
                'height' => $tipo === 'horizontal_bar' ? 420 : 360,
                'configuration' => $this->montarConfigChartJs($labels, $datasets, $tipo),
            ];

            $process = new Process(['node', base_path('scripts/render-report-chart.mjs')], base_path());
            $process->setInput(json_encode($payload, JSON_THROW_ON_ERROR));
            $process->setTimeout(60);
            $process->run();

            if (! $process->isSuccessful()) {
                return null;
            }

            return 'data:image/png;base64,' . base64_encode($process->getOutput());
        } catch (Throwable) {
            return null;
        }
    }

    private function montarConfigChartJs(array $labels, array $datasets, string $tipo): array
    {
        return match ($tipo) {
            'line' => $this->montarConfigLinha($labels, $datasets),
            'donut' => $this->montarConfigRosca($labels, $datasets),
            'horizontal_bar' => $this->montarConfigBarras($labels, $datasets, true),
            default => $this->montarConfigBarras($labels, $datasets),
        };
    }

    private function montarConfigLinha(array $labels, array $datasets): array
    {
        $chartDatasets = collect($datasets)
            ->values()
            ->map(fn (array $dataset, int $index): array => [
                'label' => $dataset['label'] ?? 'Série',
                'data' => array_values($dataset['data'] ?? []),
                'borderColor' => $dataset['color'] ?? '#dc3545',
                'backgroundColor' => $this->corTransparente($dataset['color'] ?? '#dc3545'),
                'borderWidth' => 3,
                'pointRadius' => 3,
                'pointBackgroundColor' => '#ffffff',
                'pointBorderWidth' => 2,
                'fill' => false,
                'tension' => 0.35,
                'yAxisID' => $index === 0 ? 'y' : 'y1',
            ])
            ->all();

        $scales = [
            'y' => ['beginAtZero' => true],
        ];

        if (count($chartDatasets) > 1) {
            $scales['y1'] = [
                'beginAtZero' => true,
                'position' => 'right',
                'grid' => ['drawOnChartArea' => false],
            ];
        }

        return $this->configBase('line', $labels, $chartDatasets, ['scales' => $scales]);
    }

    private function montarConfigBarras(array $labels, array $datasets, bool $horizontal = false): array
    {
        $chartDatasets = collect($datasets)
            ->map(fn (array $dataset): array => [
                'label' => $dataset['label'] ?? 'Série',
                'data' => array_values($dataset['data'] ?? []),
                'backgroundColor' => $dataset['color'] ?? '#dc3545',
                'borderRadius' => 6,
            ])
            ->all();

        $axis = $horizontal ? 'x' : 'y';
        $options = [
            'scales' => [
                $axis => ['beginAtZero' => true],
            ],
        ];

        if ($horizontal) {
            $options['indexAxis'] = 'y';
        }

        return $this->configBase('bar', $labels, $chartDatasets, $options);
    }

    private function montarConfigRosca(array $labels, array $datasets): array
    {
        $dataset = $datasets[0] ?? ['label' => 'Total', 'data' => []];

        return $this->configBase('doughnut', $labels, [[
            'label' => $dataset['label'] ?? 'Total',
            'data' => array_values($dataset['data'] ?? []),
            'backgroundColor' => collect($labels)->keys()->map(fn (int $index): string => $this->corGrafico($index))->all(),
            'borderColor' => '#ffffff',
            'borderWidth' => 2,
        ]], [
            'cutout' => '58%',
            'plugins' => [
                'legend' => ['position' => 'right'],
            ],
        ]);
    }

    private function configBase(string $tipo, array $labels, array $datasets, array $options = []): array
    {
        return [
            'type' => $tipo,
            'data' => [
                'labels' => array_values($labels),
                'datasets' => $datasets,
            ],
            'options' => array_replace_recursive([
                'responsive' => false,
                'animation' => false,
                'plugins' => [
                    'legend' => ['position' => 'bottom'],
                ],
                'font' => [
                    'family' => 'Arial',
                ],
            ], $options),
        ];
    }

    private function corTransparente(string $cor): string
    {
        $hex = ltrim($cor, '#');

        if (strlen($hex) !== 6) {
            return 'rgba(220, 53, 69, 0.14)';
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "rgba({$r}, {$g}, {$b}, 0.14)";
    }

    private function corGrafico(int $indice): string
    {
        return ['#dc3545', '#0d6efd', '#198754', '#ffc107', '#6f42c1', '#fd7e14', '#20c997', '#6c757d'][$indice % 8];
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
            ->where('doacoes.status', 'confirmada')
            ->when($this->filtroDataInicio !== '', fn (Builder $query) => $query->whereDate('doacoes.data_coleta', '>=', $this->filtroDataInicio))
            ->when($this->filtroDataFim !== '', fn (Builder $query) => $query->whereDate('doacoes.data_coleta', '<=', $this->filtroDataFim))
            ->when(! empty($this->filtroLocalColetaIds()), function (Builder $query): void {
                $query->whereHas('agendamento.campanha', fn (Builder $query) => $query->whereIn('local_coleta_id', $this->filtroLocalColetaIds()));
            })
            ->when(! empty($this->filtroTipoSanguineo), function (Builder $query): void {
                $query->whereHas('agendamento.user', fn (Builder $query) => $query->whereIn('tipo_sanguineo', $this->filtroTipoSanguineo));
            })
            ->when(! empty($this->filtroStatusAgendamento), function (Builder $query): void {
                $query->whereHas('agendamento', fn (Builder $query) => $query->whereIn('status', $this->filtroStatusAgendamento));
            })
            ->orderBy('doacoes.data_coleta');
    }

    private function bolsasAnaliticasQuery(): Builder
    {
        return BolsaSangue::query()
            ->when($this->filtroDataInicio !== '', fn (Builder $query) => $query->whereDate('data_coleta', '>=', $this->filtroDataInicio))
            ->when($this->filtroDataFim !== '', fn (Builder $query) => $query->whereDate('data_coleta', '<=', $this->filtroDataFim))
            ->when(! empty($this->filtroLocalColetaIds()), fn (Builder $query) => $query->whereIn('local_coleta_id', $this->filtroLocalColetaIds()))
            ->when(! empty($this->filtroTipoSanguineo), fn (Builder $query) => $query->whereIn('tipo_sanguineo', $this->filtroTipoSanguineo))
            ->when(! empty($this->filtroStatusBolsa), fn (Builder $query) => $query->whereIn('status', $this->filtroStatusBolsa));
    }

    private function campanhasAnaliticasQuery(): Builder
    {
        return Campanha::query()
            ->with(['agendamentos.doacao', 'localColeta'])
            ->when($this->filtroDataInicio !== '', fn (Builder $query) => $query->whereDate('campanhas.data_inicio', '>=', $this->filtroDataInicio))
            ->when($this->filtroDataFim !== '', fn (Builder $query) => $query->whereDate('campanhas.data_fim', '<=', $this->filtroDataFim))
            ->when(! empty($this->filtroStatusCampanha), fn (Builder $query) => $query->whereIn('campanhas.status', $this->filtroStatusCampanha))
            ->when(! empty($this->filtroLocalColetaIds()), fn (Builder $query) => $query->whereIn('campanhas.local_coleta_id', $this->filtroLocalColetaIds()));
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

            $cards[] = ['titulo' => 'Agendamentos', 'valor' => number_format($totalAgendamentos, 0, ',', '.'), 'detalhe' => 'registros no filtro'];
            $cards[] = ['titulo' => 'Comparecimento', 'valor' => $this->percentual($totalRealizados, $totalAgendamentos) . '%', 'detalhe' => "{$totalRealizados} realizados"];
            $cards[] = ['titulo' => 'Conversão em doação', 'valor' => $this->percentual($doacoesConfirmadas, $totalAgendamentos) . '%', 'detalhe' => "{$doacoesConfirmadas} doações confirmadas"];
        }

        if (in_array('bolsas', $this->modulosSelecionados, true)) {
            $bolsasDisponiveis = $bolsas->filter(fn (BolsaSangue $bolsa) => $bolsa->estaDisponivel());
            $volumeDisponivel = $bolsasDisponiveis->sum('quantidade_ml');

            $cards[] = ['titulo' => 'Estoque disponível', 'valor' => number_format($volumeDisponivel, 0, ',', '.') . ' ml', 'detalhe' => $bolsasDisponiveis->count() . ' bolsas disponíveis'];
            $cards[] = ['titulo' => 'Estoques críticos', 'valor' => (string) $this->contarEstoquesCriticos(), 'detalhe' => 'abaixo do mínimo configurado'];
        }

        if (in_array('campanhas', $this->modulosSelecionados, true)) {
            $cards[] = ['titulo' => 'Campanhas ativas', 'valor' => (string) $campanhas->where('status', 'ativa')->count(), 'detalhe' => $campanhas->count() . ' campanhas no filtro'];
        }

        if (in_array('doadores', $this->modulosSelecionados, true)) {
            $cards[] = ['titulo' => 'Doadores', 'valor' => number_format($doadores->count(), 0, ',', '.'), 'detalhe' => 'cadastros no filtro'];
        }

        return $cards;
    }

    private function contarEstoquesCriticos(): int
    {
        $localIds = $this->filtroLocalColetaIds();
        $tipos = $this->filtroTipoSanguineo;

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

    private function percentual(int $valor, int $total): int
    {
        return $total <= 0 ? 0 : (int) round(($valor / $total) * 100);
    }

    private function getQuery(string $modulo): Builder
    {
        if ($modulo === 'agendamentos') {
            return Agendamento::with(['user', 'campanha.localColeta', 'doacao'])
                ->when($this->filtroDataInicio !== '', fn (Builder $query) => $query->whereDate('data_hora', '>=', $this->filtroDataInicio))
                ->when($this->filtroDataFim !== '', fn (Builder $query) => $query->whereDate('data_hora', '<=', $this->filtroDataFim))
                ->when(! empty($this->filtroStatusAgendamento), fn (Builder $query) => $query->whereIn('agendamentos.status', $this->filtroStatusAgendamento))
                ->when(! empty($this->filtroLocalColetaIds()), function (Builder $query): void {
                    $query->whereHas('campanha', fn (Builder $query) => $query->whereIn('local_coleta_id', $this->filtroLocalColetaIds()));
                })
                ->orderByDesc('data_hora');
        }

        if ($modulo === 'bolsas') {
            return BolsaSangue::with(['doacao.agendamento.campanha.localColeta', 'localColeta'])
                ->when(! empty($this->filtroLocalColetaIds()), fn (Builder $query) => $query->whereIn('local_coleta_id', $this->filtroLocalColetaIds()))
                ->when(! empty($this->filtroTipoSanguineo), fn (Builder $query) => $query->whereIn('tipo_sanguineo', $this->filtroTipoSanguineo))
                ->when(! empty($this->filtroStatusBolsa), fn (Builder $query) => $query->whereIn('bolsas_sangue.status', $this->filtroStatusBolsa))
                ->orderByDesc('data_coleta');
        }

        if ($modulo === 'campanhas') {
            return Campanha::with(['localColeta', 'agendamentos.doacao'])
                ->when($this->filtroDataInicio !== '', fn (Builder $query) => $query->whereDate('data_inicio', '>=', $this->filtroDataInicio))
                ->when($this->filtroDataFim !== '', fn (Builder $query) => $query->whereDate('data_fim', '<=', $this->filtroDataFim))
                ->when(! empty($this->filtroStatusCampanha), fn (Builder $query) => $query->whereIn('campanhas.status', $this->filtroStatusCampanha))
                ->when(! empty($this->filtroLocalColetaIds()), fn (Builder $query) => $query->whereIn('local_coleta_id', $this->filtroLocalColetaIds()))
                ->orderByDesc('data_inicio');
        }

        if ($modulo === 'doadores') {
            return User::query()
                ->where('tipo', User::TIPO_DOADOR)
                ->when(! empty($this->filtroTipoSanguineo), fn (Builder $query) => $query->whereIn('tipo_sanguineo', $this->filtroTipoSanguineo))
                ->orderBy('name');
        }

        return Agendamento::query();
    }

    private function normalizarColunas(array $colunasSelecionadas): array
    {
        $colunas = [];

        foreach ($this->modulosSelecionados as $modulo) {
            $opcoes = array_keys(self::opcoesColunas($modulo));
            $colunas[$modulo] = array_key_exists($modulo, $colunasSelecionadas)
                ? $this->normalizarLista($colunasSelecionadas[$modulo], $opcoes)
                : $opcoes;
        }

        return $colunas;
    }

    private function normalizarLista(mixed $valores, array $permitidos): array
    {
        if (! is_array($valores)) {
            return [];
        }

        return array_values(array_intersect($valores, $permitidos));
    }

    private function filtroLocalColetaIds(): array
    {
        return collect($this->filtroLocalColeta)
            ->filter(fn (mixed $id) => ctype_digit((string) $id))
            ->map(fn (mixed $id) => (int) $id)
            ->values()
            ->all();
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

    private function getStatusBolsa(): array
    {
        return [
            BolsaSangue::STATUS_DISPONIVEL => 'Disponível',
            BolsaSangue::STATUS_UTILIZADA => 'Utilizada',
            BolsaSangue::STATUS_DESCARTADA => 'Descartada',
            BolsaSangue::STATUS_VENCIDA => 'Vencida',
            BolsaSangue::STATUS_TRANSFERIDA => 'Transferida',
        ];
    }

    private function getStatusCampanha(): array
    {
        return [
            'ativa' => 'Ativa',
            'encerrada' => 'Encerrada',
            'cancelada' => 'Cancelada',
        ];
    }
}

<?php

namespace App\Livewire\Admin;

use App\Jobs\ArquivarRelatorioPdf;
use App\Jobs\GerarRelatorioPdf;
use App\Models\Agendamento;
use App\Models\BolsaSangue;
use App\Models\LocalColeta;
use App\Models\RelatorioExport;
use App\Support\RelatorioAnaliticoBuilder;
use App\Support\RelatorioDadosBuilder;
use App\Support\TipoSanguineo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RelatorioBuilder extends Component
{
    public array $modulosSelecionados = [];
    public array $colunasSelecionadas = [];
    public string $graficoPrincipal = 'doacoes_periodo';
    public array $graficosSelecionados = [];
    public bool $incluirIndicadores = true;

    public string $filtroDataInicio = '';
    public string $filtroDataFim = '';
    public array $filtroStatusAgendamento = [];
    public array $filtroStatusBolsa = [];
    public array $filtroStatusCampanha = [];
    public array $filtroTipoSanguineo = [];
    public array $filtroLocalColeta = [];

    public bool $mostrarTudo = false;

    public function mount(): void
    {
        $this->setDefaultColumns();
    }

    public function toggleMostrarTudo(): void
    {
        $this->mostrarTudo = ! $this->mostrarTudo;
    }

    public function updatedModulosSelecionados(): void
    {
        $this->setDefaultColumns();
    }

    public function getModulos(): array
    {
        return RelatorioDadosBuilder::modulos();
    }

    public function getOpcoesColunas(string $modulo): array
    {
        return RelatorioDadosBuilder::opcoesColunas($modulo);
    }

    public function getFiltrosDisponiveis(): array
    {
        $filtros = [];

        foreach ($this->modulosSelecionados as $modulo) {
            $filtros = array_merge($filtros, match ($modulo) {
                'agendamentos' => ['datas', 'status_agendamento', 'local'],
                'bolsas' => ['tipo_sanguineo', 'status_bolsa', 'local'],
                'campanhas' => ['datas', 'status_campanha', 'local'],
                'doadores' => ['tipo_sanguineo'],
                default => [],
            });
        }

        return array_unique($filtros);
    }

    public function getLocaisColeta(): Collection
    {
        return LocalColeta::orderBy('nome')->pluck('nome', 'id');
    }

    public function getTiposSanguineos(): array
    {
        return TipoSanguineo::values();
    }

    public function getStatusAgendamento(): array
    {
        return [
            Agendamento::STATUS_AGENDADO => 'Agendado',
            Agendamento::STATUS_REALIZADO => 'Realizado',
            Agendamento::STATUS_FALTOU => 'Faltou',
            Agendamento::STATUS_CANCELADO => 'Cancelado',
        ];
    }

    public function getStatusBolsa(): array
    {
        return [
            BolsaSangue::STATUS_DISPONIVEL => 'Disponível',
            BolsaSangue::STATUS_UTILIZADA => 'Utilizada',
            BolsaSangue::STATUS_DESCARTADA => 'Descartada',
            BolsaSangue::STATUS_VENCIDA => 'Vencida',
            BolsaSangue::STATUS_TRANSFERIDA => 'Transferida',
        ];
    }

    public function getStatusCampanha(): array
    {
        return [
            'ativa' => 'Ativa',
            'encerrada' => 'Encerrada',
            'cancelada' => 'Cancelada',
        ];
    }

    public function getGraficosPrincipais(): array
    {
        return RelatorioDadosBuilder::graficosPrincipais();
    }

    public function getDados(): array
    {
        return $this->relatorioDadosBuilder()->getDados();
    }

    public function exportarCsv(): StreamedResponse
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', '300');

        $dadosPorModulo = $this->getDados();
        $nomeArquivo = 'relatorio_multiplo_' . date('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($dadosPorModulo): void {
            $handle = fopen('php://output', 'w');
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            foreach ($this->modulosSelecionados as $modulo) {
                $colunasAtivas = array_filter(
                    $this->getOpcoesColunas($modulo),
                    fn ($chave) => in_array($chave, $this->colunasSelecionadas[$modulo] ?? []),
                    ARRAY_FILTER_USE_KEY
                );

                fputcsv($handle, [$this->getModulos()[$modulo]], ';');
                fputcsv($handle, array_values($colunasAtivas), ';');

                foreach ($dadosPorModulo[$modulo] as $linha) {
                    $linhaFormatada = [];

                    foreach (array_keys($colunasAtivas) as $coluna) {
                        $linhaFormatada[] = $this->formatarValor($linha, $modulo, $coluna);
                    }

                    fputcsv($handle, $linhaFormatada, ';');
                }

                fputcsv($handle, [], ';');
            }

            fclose($handle);
        }, $nomeArquivo);
    }

    public function exportarPdf(): void
    {
        if (empty($this->modulosSelecionados)) {
            $this->dispatch('alert-error', message: 'Selecione ao menos um módulo para gerar o PDF.');

            return;
        }

        $this->setDefaultColumns();

        $exportacao = RelatorioExport::create([
            'user_id' => auth()->id(),
            'tipo' => RelatorioExport::TIPO_PDF,
            'status' => RelatorioExport::STATUS_PENDENTE,
            'parametros' => $this->getParametrosRelatorio(),
        ]);

        GerarRelatorioPdf::dispatch($exportacao->id);

        $this->dispatch('alert-success', message: 'PDF enviado para processamento.');
    }

    public function formatarValor($modelo, string $modulo, string $coluna): string
    {
        return $this->relatorioDadosBuilder()->formatarValor($modelo, $modulo, $coluna);
    }

    public function arquivarPdf(int $id): void
    {
        $exportacao = RelatorioExport::where('user_id', auth()->id())->findOrFail($id);

        if ($exportacao->is_arquivado || ! $exportacao->concluido()) {
            return;
        }

        $exportacao->forceFill([
            'status' => RelatorioExport::STATUS_ARQUIVANDO,
        ])->save();

        ArquivarRelatorioPdf::dispatch($exportacao->id);

        $this->dispatch('alert-success', message: 'Relatório enviado para arquivamento.');
    }

    public function excluirPdf(int $id): void
    {
        $exportacao = RelatorioExport::where('user_id', auth()->id())->findOrFail($id);

        if ($exportacao->arquivo_path && Storage::disk('local')->exists($exportacao->arquivo_path)) {
            Storage::disk('local')->delete($exportacao->arquivo_path);
        }

        $exportacao->delete();

        $this->dispatch('alert-success', message: 'Relatório excluído com sucesso.');
    }

    public function render(): View
    {
        $dadosPorModulo = $this->getDados();
        $analiticoBuilder = $this->relatorioAnaliticoBuilder();

        return view('livewire.admin.relatorio-builder', [
            'dadosPorModulo' => $dadosPorModulo,
            'exportacoesPdf' => $this->getExportacoesPdf(),
            'graficoPrincipalData' => $analiticoBuilder->getGraficoPrincipal(),
            'painelAnalitico' => $analiticoBuilder->getPainelAnalitico($dadosPorModulo),
        ]);
    }

    private function setDefaultColumns(): void
    {
        foreach ($this->modulosSelecionados as $modulo) {
            if (! isset($this->colunasSelecionadas[$modulo])) {
                $this->colunasSelecionadas[$modulo] = array_keys($this->getOpcoesColunas($modulo));
            }
        }
    }

    private function relatorioDadosBuilder(): RelatorioDadosBuilder
    {
        return new RelatorioDadosBuilder($this->getParametrosRelatorio());
    }

    private function relatorioAnaliticoBuilder(): RelatorioAnaliticoBuilder
    {
        return new RelatorioAnaliticoBuilder($this->getParametrosRelatorio(), $this->graficoPrincipal);
    }

    private function getParametrosRelatorio(): array
    {
        return [
            'modulosSelecionados' => $this->modulosSelecionados,
            'colunasSelecionadas' => $this->colunasSelecionadas,
            'filtroDataInicio' => $this->filtroDataInicio,
            'filtroDataFim' => $this->filtroDataFim,
            'filtroStatusAgendamento' => $this->filtroStatusAgendamento,
            'filtroStatusBolsa' => $this->filtroStatusBolsa,
            'filtroStatusCampanha' => $this->filtroStatusCampanha,
            'filtroTipoSanguineo' => $this->filtroTipoSanguineo,
            'filtroLocalColeta' => $this->filtroLocalColeta,
            'graficosSelecionados' => $this->graficosSelecionados,
            'incluirIndicadores' => $this->incluirIndicadores,
        ];
    }

    private function getExportacoesPdf(): Collection
    {
        return RelatorioExport::query()
            ->where('user_id', auth()->id())
            ->where('tipo', RelatorioExport::TIPO_PDF)
            ->where('is_arquivado', false)
            ->latest()
            ->limit(3)
            ->get();
    }
}

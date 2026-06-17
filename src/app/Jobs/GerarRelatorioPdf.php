<?php

namespace App\Jobs;

use App\Models\RelatorioExport;
use App\Support\RelatorioDadosBuilder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class GerarRelatorioPdf implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 300;

    public function __construct(public int $relatorioExportId)
    {
    }

    public function handle(): void
    {
        ini_set('memory_limit', '1024M');

        $exportacao = RelatorioExport::findOrFail($this->relatorioExportId);

        if ($exportacao->concluido()) {
            return;
        }

        $exportacao->forceFill([
            'status' => RelatorioExport::STATUS_PROCESSANDO,
            'erro' => null,
            'started_at' => $exportacao->started_at ?? now(),
        ])->save();

        try {
            $builder = new RelatorioDadosBuilder($exportacao->parametros ?? []);
            $dadosPorModulo = $builder->getDados();
            $painelAnalitico = $builder->getPainelAnalitico($dadosPorModulo);
            $graficosPdf = $builder->getGraficosSelecionados();

            $pdf = Pdf::loadView('admin.relatorios.pdf', [
                'titulo' => 'Relatório Consolidado',
                'modulosSelecionados' => $builder->modulosSelecionados,
                'dadosPorModulo' => $dadosPorModulo,
                'painelAnalitico' => $painelAnalitico,
                'graficosPdf' => $graficosPdf,
                'builder' => $builder,
            ])->setPaper('a4', 'landscape');

            $arquivoPath = "relatorios/relatorio-{$exportacao->id}.pdf";

            if (! Storage::disk('local')->put($arquivoPath, $pdf->output())) {
                throw new RuntimeException('Não foi possível salvar o PDF gerado.');
            }

            $exportacao->forceFill([
                'status' => RelatorioExport::STATUS_CONCLUIDO,
                'arquivo_path' => $arquivoPath,
                'erro' => null,
                'finished_at' => now(),
            ])->save();
        } catch (Throwable $exception) {
            $exportacao->forceFill([
                'erro' => $this->resumirErro($exception),
            ])->save();

            throw $exception;
        }
    }

    public function failed(Throwable $exception): void
    {
        RelatorioExport::query()
            ->whereKey($this->relatorioExportId)
            ->update([
                'status' => RelatorioExport::STATUS_FALHOU,
                'erro' => $this->resumirErro($exception),
                'finished_at' => now(),
            ]);
    }

    private function resumirErro(Throwable $exception): string
    {
        return mb_strimwidth($exception->getMessage(), 0, 1000, '...');
    }
}

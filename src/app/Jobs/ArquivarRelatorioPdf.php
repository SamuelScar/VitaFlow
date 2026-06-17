<?php

namespace App\Jobs;

use App\Models\RelatorioExport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;
use ZipArchive;

class ArquivarRelatorioPdf implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 300;

    public function __construct(public int $relatorioExportId)
    {
    }

    public function handle(): void
    {
        $exportacao = RelatorioExport::findOrFail($this->relatorioExportId);

        if ($exportacao->is_arquivado || $exportacao->status !== RelatorioExport::STATUS_ARQUIVANDO) {
            return;
        }

        try {
            $pdfPath = Storage::disk('local')->path($exportacao->arquivo_path);

            if (!file_exists($pdfPath)) {
                throw new RuntimeException('Arquivo PDF não encontrado para arquivamento.');
            }

            $zipDir = Storage::disk('local')->path('relatorios/arquivados');
            if (!is_dir($zipDir)) {
                mkdir($zipDir, 0755, true);
            }

            $zipFileName = "relatorio-{$exportacao->id}.zip";
            $zipPath = $zipDir . '/' . $zipFileName;

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                // Compress with max level
                $zip->addFile($pdfPath, basename($pdfPath));
                $zip->setCompressionIndex(0, ZipArchive::CM_DEFLATE, 9);
                $zip->close();
            } else {
                throw new RuntimeException('Não foi possível criar o arquivo ZIP.');
            }

            // Verify if zip was created and delete original
            if (file_exists($zipPath)) {
                Storage::disk('local')->delete($exportacao->arquivo_path);
            }

            $exportacao->forceFill([
                'status' => RelatorioExport::STATUS_CONCLUIDO,
                'is_arquivado' => true,
                'arquivo_path' => "relatorios/arquivados/{$zipFileName}",
                'erro' => null,
            ])->save();

        } catch (Throwable $exception) {
            $exportacao->forceFill([
                'status' => RelatorioExport::STATUS_FALHOU,
                'erro' => mb_strimwidth($exception->getMessage(), 0, 1000, '...'),
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
                'erro' => mb_strimwidth($exception->getMessage(), 0, 1000, '...'),
            ]);
    }
}

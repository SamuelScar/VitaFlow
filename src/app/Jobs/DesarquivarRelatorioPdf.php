<?php

namespace App\Jobs;

use App\Models\RelatorioExport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;
use ZipArchive;

class DesarquivarRelatorioPdf implements ShouldQueue
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

        if (!$exportacao->is_arquivado || $exportacao->status !== RelatorioExport::STATUS_DESARQUIVANDO) {
            return;
        }

        try {
            $zipPath = Storage::disk('local')->path($exportacao->arquivo_path);

            if (!file_exists($zipPath)) {
                throw new RuntimeException('Arquivo ZIP não encontrado para desarquivamento.');
            }

            $extractDir = Storage::disk('local')->path('relatorios');
            if (!is_dir($extractDir)) {
                mkdir($extractDir, 0755, true);
            }

            $pdfFileName = "relatorio-{$exportacao->id}.pdf";
            
            $zip = new ZipArchive();
            if ($zip->open($zipPath) === true) {
                // Determine the name of the file inside the zip
                $zipFileEntry = $zip->getNameIndex(0);
                if (!$zipFileEntry) {
                    $zipFileEntry = $pdfFileName; // fallback
                }
                
                $zip->extractTo($extractDir, $zipFileEntry);
                $zip->close();
                
                // If the extracted file has a different name somehow, rename it
                if ($zipFileEntry !== $pdfFileName && file_exists($extractDir . '/' . $zipFileEntry)) {
                    rename($extractDir . '/' . $zipFileEntry, $extractDir . '/' . $pdfFileName);
                }

            } else {
                throw new RuntimeException('Não foi possível extrair o arquivo ZIP.');
            }

            // Verify if pdf was extracted and delete zip
            $pdfPathRel = "relatorios/{$pdfFileName}";
            if (Storage::disk('local')->exists($pdfPathRel)) {
                Storage::disk('local')->delete($exportacao->arquivo_path);
            }

            $exportacao->forceFill([
                'status' => RelatorioExport::STATUS_CONCLUIDO,
                'is_arquivado' => false,
                'arquivo_path' => $pdfPathRel,
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

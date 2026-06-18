<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RelatorioExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RelatorioController extends Controller
{
    public function index(): View
    {
        return view('admin.relatorios.index');
    }

    public function download(RelatorioExport $relatorioExport): StreamedResponse
    {
        abort_unless($relatorioExport->user_id === auth()->id(), 403);
        abort_unless($relatorioExport->concluido(), 404);
        abort_unless(Storage::disk('local')->exists($relatorioExport->arquivo_path), 404);

        $extensao = $relatorioExport->is_arquivado ? 'zip' : 'pdf';

        return Storage::disk('local')->download(
            $relatorioExport->arquivo_path,
            "relatorio-{$relatorioExport->id}.{$extensao}"
        );
    }
}

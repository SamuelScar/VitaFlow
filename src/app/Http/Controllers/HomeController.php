<?php

namespace App\Http\Controllers;

use App\Models\Campanha;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Exibe a home pública com as campanhas de doação atualmente ativas e dentro do período vigente.
 */
class HomeController extends Controller
{
    private const ITENS_POR_PAGINA = [6, 12, 24, 48];

    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $itensPorPagina = $request->integer('por_pagina', 12);
        $itensPorPagina = in_array($itensPorPagina, self::ITENS_POR_PAGINA, true)
            ? $itensPorPagina
            : 12;

        $query = Campanha::query()
            ->where('status', 'ativa')
            ->whereDate('data_inicio', '<=', now())
            ->whereDate('data_fim', '>=', now());

        $resumo = (clone $query)
            ->selectRaw('COUNT(*) as total_campanhas')
            ->selectRaw('COALESCE(SUM(meta_bolsas), 0) as total_meta_bolsas')
            ->selectRaw('COUNT(DISTINCT local_coleta_id) as total_locais')
            ->firstOrFail();

        if ($user?->isDoador()) {
            $query->withExists([
                'agendamentos as usuario_agendado' => fn ($query) => $query->where('user_id', $user->id),
            ]);
        }

        return view('home', [
            'campanhas' => $query
                ->with('localColeta')
                ->orderBy('data_fim')
                ->orderBy('titulo')
                ->paginate($itensPorPagina)
                ->withQueryString(),
            'itensPorPagina' => $itensPorPagina,
            'opcoesPorPagina' => self::ITENS_POR_PAGINA,
            'totalCampanhas' => (int) $resumo->total_campanhas,
            'totalMetaBolsas' => (int) $resumo->total_meta_bolsas,
            'totalLocais' => (int) $resumo->total_locais,
        ]);
    }
}

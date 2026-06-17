<?php

namespace App\Http\Controllers\Doador;

use App\Http\Controllers\Controller;
use App\Models\Doacao;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoacaoController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        assert($user !== null);

        $doacoes = Doacao::with(['agendamento.campanha.localColeta', 'bolsaSangue'])
            ->whereHas('agendamento', function ($query) use ($user): void {
                $query->where('user_id', $user->id);
            })
            ->latest('data_coleta')
            ->paginate(10)
            ->withQueryString();

        $totaisQuery = Doacao::whereHas('agendamento', function ($query) use ($user): void {
            $query->where('user_id', $user->id);
        })->where('status', 'confirmada');

        $totalDoacoes = $totaisQuery->count();
        $totalMl = $totaisQuery->sum('quantidade_ml');

        return view('usuario.doacoes.index', [
            'doacoes' => $doacoes,
            'totalDoacoes' => $totalDoacoes,
            'totalMl' => $totalMl,
        ]);
    }
}

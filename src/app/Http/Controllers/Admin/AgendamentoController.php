<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Exibe a visao administrativa de acompanhamento dos agendamentos.
 */
class AgendamentoController extends Controller
{
    public function index(): View
    {
        return view('admin.agendamentos.index');
    }

    public function show(\App\Models\Agendamento $agendamento): View
    {
        $agendamento->load(['user.carteiraDoacao', 'campanha.localColeta', 'doacao.bolsaSangue']);

        return view('admin.agendamentos.show', [
            'agendamento' => $agendamento,
        ]);
    }
}

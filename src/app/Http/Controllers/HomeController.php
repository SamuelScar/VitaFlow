<?php

namespace App\Http\Controllers;

use App\Models\Campanha;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'campanhas' => Campanha::with('localColeta')
                ->where('status', 'ativa')
                ->whereDate('data_inicio', '<=', now())
                ->whereDate('data_fim', '>=', now())
                ->orderBy('data_fim')
                ->orderBy('titulo')
                ->get(),
        ]);
    }
}

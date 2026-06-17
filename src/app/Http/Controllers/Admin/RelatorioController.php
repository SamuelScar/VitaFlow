<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class RelatorioController extends Controller
{
    /**
     * Display the dynamic report builder page.
     */
    public function index(): View
    {
        return view('admin.relatorios.index');
    }
}

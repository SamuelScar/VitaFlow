<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Exibe a gestão administrativa de bolsas e o estoque calculado.
 */
class BolsaSangueController extends Controller
{
    public function index(): View
    {
        return view('admin.bolsas-sangue.index');
    }
}

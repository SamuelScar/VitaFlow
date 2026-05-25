<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class UserPromotionController extends Controller
{
    public function __invoke(User $user): RedirectResponse
    {
        $user->promoteToAdmin();

        return back()->with('success', 'Usuario promovido para administrador.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

/**
 * Promove um usuário doador para o perfil de administrador. A operação é idempotente — se o usuário já for admin, nada acontece.
 */
class UserPromotionController extends Controller
{
    public function __invoke(User $user): RedirectResponse
    {
        $user->promoteToAdmin();

        return back()->with('success', 'Usuario promovido para administrador.');
    }
}

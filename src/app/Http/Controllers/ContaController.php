<?php

namespace App\Http\Controllers;

use App\Rules\DifferentFromCurrentPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ContaController extends Controller
{
    public function edit(): View
    {
        return view('conta.edit');
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            abort(403);
        }

        $data = $request->validateWithBag('updateConta', [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => ['required_with:password', 'current_password'],
            'password' => [
                'nullable',
                'confirmed',
                Password::min(8),
                new DifferentFromCurrentPassword($user->password),
            ],
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            ...($request->filled('password') ? ['password' => $data['password']] : []),
        ]);

        return back()->with('success', 'Dados da conta atualizados com sucesso.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            abort(403);
        }

        $request->validateWithBag('deleteConta', [
            'password' => ['required', 'current_password'],
        ]);

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Conta excluida com sucesso.');
    }
}

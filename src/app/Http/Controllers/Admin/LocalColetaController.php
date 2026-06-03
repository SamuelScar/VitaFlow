<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LocalColeta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Gerencia o CRUD de locais de coleta. O endereço é normalizado (CEP formatado, UF em maiúsculas) antes da validação.
 */
class LocalColetaController extends Controller
{
    public function index(): View
    {
        return view('admin.locais-coleta.index', [
            'locaisColeta' => LocalColeta::orderBy('nome')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->normalizeAddress($request);

        LocalColeta::create($request->validateWithBag('storeLocalColeta', $this->rules()));

        return back()->with('success', 'Local de coleta cadastrado com sucesso.');
    }

    public function update(Request $request, LocalColeta $localColeta): RedirectResponse
    {
        $this->normalizeAddress($request);

        $localColeta->update($request->validateWithBag('updateLocalColeta', $this->rules()));

        return back()->with('success', 'Local de coleta atualizado com sucesso.');
    }

    public function destroy(LocalColeta $localColeta): RedirectResponse
    {
        if ($localColeta->campanhas()->exists() || $localColeta->estoquesSangue()->exists()) {
            return back()->withErrors([
                'local_coleta' => 'Nao e possivel excluir um local de coleta com campanhas ou estoque vinculado.',
            ]);
        }

        $localColeta->delete();

        return back()->with('success', 'Local de coleta excluido com sucesso.');
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'cep' => ['required', 'string', 'regex:/^\d{5}-\d{3}$/'],
            'logradouro' => ['required', 'string', 'max:255'],
            'numero' => ['required', 'string', 'max:30'],
            'bairro' => ['required', 'string', 'max:255'],
            'cidade' => ['required', 'string', 'max:255'],
            'uf' => ['required', 'string', 'size:2'],
            'complemento' => ['nullable', 'string', 'max:255'],
            'capacidade_diaria' => ['required', 'integer', 'min:1', 'max:10000'],
        ];
    }

    /**
     * Normaliza o CEP para o formato NNNNN-NNN e converte a UF para maiúsculas diretamente no request antes da validação.
     */
    private function normalizeAddress(Request $request): void
    {
        $cep = preg_replace('/\D/', '', (string) $request->input('cep', ''));

        $request->merge([
            'cep' => strlen($cep) === 8 ? substr($cep, 0, 5) . '-' . substr($cep, 5) : $cep,
            'uf' => strtoupper((string) $request->input('uf', '')),
        ]);
    }
}

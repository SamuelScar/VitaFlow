<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nome',
    'cep',
    'logradouro',
    'numero',
    'bairro',
    'cidade',
    'uf',
    'complemento',
    'capacidade_diaria',
])]
class LocalColeta extends Model
{
    protected $table = 'locais_coleta';

    public function campanhas(): HasMany
    {
        return $this->hasMany(Campanha::class);
    }

    public function estoquesSangue(): HasMany
    {
        return $this->hasMany(EstoqueSangue::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'capacidade_diaria' => 'integer',
        ];
    }

    /**
     * @return Attribute<string, never>
     */
    protected function enderecoCompleto(): Attribute
    {
        return Attribute::get(function (): string {
            $logradouro = trim(implode(', ', array_filter([
                $this->logradouro,
                $this->numero,
            ])));

            $cidade = $this->uf ? "{$this->cidade}/{$this->uf}" : $this->cidade;

            return trim(implode(', ', array_filter([
                trim(implode(' - ', array_filter([$logradouro, $this->bairro, $this->complemento]))),
                $cidade,
                $this->cep ? "CEP {$this->cep}" : null,
            ])));
        });
    }
}

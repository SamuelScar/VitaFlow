<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nome', 'endereco', 'cidade', 'capacidade_diaria'])]
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
}

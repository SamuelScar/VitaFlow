<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'local_coleta_id',
    'tipo_sanguineo',
    'quantidade_ml',
    'bolsas_disponiveis',
    'estoque_minimo_ml',
])]
/**
 * Representa o estoque de sangue de um tipo específico em um local de coleta.
 * Cada local possui no máximo um registro por tipo sanguíneo.
 */
class EstoqueSangue extends Model
{
    protected $table = 'estoques_sangue';

    /**
     * Retorna o local de coleta ao qual este estoque pertence.
     */
    public function localColeta(): BelongsTo
    {
        return $this->belongsTo(LocalColeta::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantidade_ml' => 'integer',
            'bolsas_disponiveis' => 'integer',
            'estoque_minimo_ml' => 'integer',
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'local_coleta_id',
    'tipo_sanguineo',
    'estoque_minimo_ml',
])]
/**
 * Representa a configuração de estoque mínimo de um tipo sanguíneo em um local.
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
            'estoque_minimo_ml' => 'integer',
        ];
    }
}

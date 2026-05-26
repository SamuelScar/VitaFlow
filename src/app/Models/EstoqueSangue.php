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
class EstoqueSangue extends Model
{
    protected $table = 'estoques_sangue';

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

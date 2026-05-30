<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'local_coleta_id',
    'titulo',
    'descricao',
    'tipos_sanguineos_alvo',
    'meta_bolsas',
    'data_inicio',
    'data_fim',
    'status',
])]
class Campanha extends Model
{
    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criada_por_id');
    }

    public function localColeta(): BelongsTo
    {
        return $this->belongsTo(LocalColeta::class);
    }

    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tipos_sanguineos_alvo' => 'array',
            'meta_bolsas' => 'integer',
            'data_inicio' => 'date',
            'data_fim' => 'date',
        ];
    }
}

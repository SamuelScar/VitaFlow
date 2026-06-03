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
/**
 * Representa uma campanha de doação de sangue. Pode ter tipos sanguíneos alvo
 * específicos (campo JSON) ou aceitar todos os tipos quando `tipos_sanguineos_alvo` é null.
 */
class Campanha extends Model
{
    /**
     * Retorna o usuário administrador que criou a campanha.
     */
    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criada_por_id');
    }

    /**
     * Retorna o local de coleta onde a campanha acontece.
     */
    public function localColeta(): BelongsTo
    {
        return $this->belongsTo(LocalColeta::class);
    }

    /**
     * Retorna todos os agendamentos de doadores vinculados a esta campanha.
     */
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

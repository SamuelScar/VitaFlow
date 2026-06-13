<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'status',
    'emitida_em',
])]
/**
 * Representa a credencial que autoriza o usuário doador a realizar agendamentos.
 * Cada usuário pode ter no máximo uma carteirinha.
 */
class CarteiraDoacao extends Model
{
    protected $table = 'carteiras_doacao';

    /**
     * Retorna o usuário dono desta carteirinha.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'emitida_em' => 'date',
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'cpf',
    'telefone',
    'data_nascimento',
    'tipo_sanguineo',
    'peso',
    'cidade',
    'status',
    'emitida_em',
])]
/**
 * Representa a carteirinha de doador — dados complementares do usuário para participar
 * de campanhas de doação. Cada usuário pode ter no máximo uma carteirinha.
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
     * Retorna todos os agendamentos feitos com esta carteirinha.
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
            'data_nascimento' => 'date',
            'peso' => 'decimal:2',
            'emitida_em' => 'date',
        ];
    }
}

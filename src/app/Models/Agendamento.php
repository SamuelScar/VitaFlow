<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['carteira_doacao_id', 'campanha_id', 'data_hora', 'status'])]
/**
 * Representa o agendamento de um doador para uma campanha. Um doador não pode
 * se agendar mais de uma vez na mesma campanha (constraint única no banco).
 */
class Agendamento extends Model
{
    /**
     * Retorna a carteirinha do doador que fez o agendamento.
     */
    public function carteiraDoacao(): BelongsTo
    {
        return $this->belongsTo(CarteiraDoacao::class);
    }

    /**
     * Retorna a campanha para a qual o agendamento foi feito.
     */
    public function campanha(): BelongsTo
    {
        return $this->belongsTo(Campanha::class);
    }

    /**
     * Retorna o registro de doação vinculado a este agendamento, se houver.
     */
    public function doacao(): HasOne
    {
        return $this->hasOne(Doacao::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_hora' => 'datetime',
        ];
    }
}

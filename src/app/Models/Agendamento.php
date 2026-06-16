<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['user_id', 'campanha_id', 'data_hora', 'status'])]
/**
 * Representa o agendamento de um doador para uma campanha. Um doador não pode
 * se agendar mais de uma vez na mesma campanha (constraint única no banco).
 */
class Agendamento extends Model
{
    /**
     * Retorna o usuário doador que fez o agendamento.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function podeSerGerenciadoPeloDoador(): bool
    {
        return $this->status === 'agendado'
            && $this->data_hora->greaterThanOrEqualTo(now());
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

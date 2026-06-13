<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['agendamento_id', 'data_coleta', 'quantidade_ml', 'status', 'motivo_recusa'])]
/**
 * Representa o resultado de uma coleta de sangue vinculada a um agendamento.
 * Cada agendamento pode ter no máximo uma doação registrada.
 */
class Doacao extends Model
{
    protected $table = 'doacoes';

    /**
     * Retorna o agendamento ao qual esta doação está vinculada.
     */
    public function agendamento(): BelongsTo
    {
        return $this->belongsTo(Agendamento::class);
    }

    public function bolsaSangue(): HasOne
    {
        return $this->hasOne(BolsaSangue::class);
    }

    protected static function booted(): void
    {
        static::saved(function (Doacao $doacao): void {
            if ($doacao->status !== 'confirmada' || $doacao->quantidade_ml === null) {
                return;
            }

            $doacao->loadMissing('agendamento.campanha', 'agendamento.user');
            $agendamento = $doacao->agendamento;

            if ($agendamento?->campanha === null || $agendamento->user?->tipo_sanguineo === null) {
                return;
            }

            $doacao->bolsaSangue()->firstOrCreate([], [
                'local_coleta_id' => $agendamento->campanha->local_coleta_id,
                'tipo_sanguineo' => $agendamento->user->tipo_sanguineo,
                'quantidade_ml' => $doacao->quantidade_ml,
                'data_coleta' => $doacao->data_coleta,
                'validade_em' => $doacao->data_coleta->copy()->addDays((int) env('BOLSA_SANGUE_VALIDADE_DIAS', 42)),
                'status' => BolsaSangue::STATUS_DISPONIVEL,
            ]);
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_coleta' => 'datetime',
            'quantidade_ml' => 'integer',
        ];
    }
}

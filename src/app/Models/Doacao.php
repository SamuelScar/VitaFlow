<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['agendamento_id', 'data_coleta', 'quantidade_ml', 'status', 'motivo_recusa'])]
class Doacao extends Model
{
    protected $table = 'doacoes';

    public function agendamento(): BelongsTo
    {
        return $this->belongsTo(Agendamento::class);
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

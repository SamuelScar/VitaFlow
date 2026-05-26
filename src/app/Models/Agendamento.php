<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['carteira_doacao_id', 'campanha_id', 'data_hora', 'status'])]
class Agendamento extends Model
{
    public function carteiraDoacao(): BelongsTo
    {
        return $this->belongsTo(CarteiraDoacao::class);
    }

    public function campanha(): BelongsTo
    {
        return $this->belongsTo(Campanha::class);
    }

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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'doacao_id',
    'local_coleta_id',
    'tipo_sanguineo',
    'quantidade_ml',
    'data_coleta',
    'validade_em',
    'status',
])]
/**
 * Representa uma bolsa armazenada e seu ciclo de vida após uma doação confirmada.
 */
class BolsaSangue extends Model
{
    public const STATUS_DISPONIVEL = 'disponivel';
    public const STATUS_UTILIZADA = 'utilizada';
    public const STATUS_VENCIDA = 'vencida';
    public const STATUS_DESCARTADA = 'descartada';
    public const STATUS_TRANSFERIDA = 'transferida';

    protected $table = 'bolsas_sangue';

    public function doacao(): BelongsTo
    {
        return $this->belongsTo(Doacao::class);
    }

    public function localColeta(): BelongsTo
    {
        return $this->belongsTo(LocalColeta::class);
    }

    public function statusAtual(): string
    {
        return $this->estaVencida() ? self::STATUS_VENCIDA : $this->status;
    }

    public function estaVencida(): bool
    {
        return $this->validade_em->isPast()
            && in_array($this->status, [self::STATUS_DISPONIVEL, self::STATUS_TRANSFERIDA], true);
    }

    public function estaDisponivel(): bool
    {
        return ! $this->estaVencida()
            && in_array($this->status, [self::STATUS_DISPONIVEL, self::STATUS_TRANSFERIDA], true);
    }

    public function utilizar(): void
    {
        $this->forceFill(['status' => self::STATUS_UTILIZADA])->save();
    }

    public function descartar(): void
    {
        $this->forceFill(['status' => self::STATUS_DESCARTADA])->save();
    }

    public function transferir(LocalColeta $destino): void
    {
        $this->forceFill([
            'local_coleta_id' => $destino->id,
            'status' => self::STATUS_TRANSFERIDA,
        ])->save();
    }

    public function scopeDisponiveis(Builder $query): Builder
    {
        return $query
            ->whereIn('status', [self::STATUS_DISPONIVEL, self::STATUS_TRANSFERIDA])
            ->where('validade_em', '>', now());
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantidade_ml' => 'integer',
            'data_coleta' => 'datetime',
            'validade_em' => 'datetime',
        ];
    }
}

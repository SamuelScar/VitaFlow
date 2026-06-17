<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id',
    'tipo',
    'status',
    'is_arquivado',
    'parametros',
    'arquivo_path',
    'erro',
    'started_at',
    'finished_at',
])]
class RelatorioExport extends Model
{
    use SoftDeletes;

    public const TIPO_PDF = 'pdf';
    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_PROCESSANDO = 'processando';
    public const STATUS_CONCLUIDO = 'concluido';
    public const STATUS_FALHOU = 'falhou';
    public const STATUS_ARQUIVANDO = 'arquivando';
    public const STATUS_DESARQUIVANDO = 'desarquivando';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function concluido(): bool
    {
        return $this->status === self::STATUS_CONCLUIDO && $this->arquivo_path !== null;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDENTE => 'Pendente',
            self::STATUS_PROCESSANDO => 'Processando',
            self::STATUS_CONCLUIDO => 'Concluído',
            self::STATUS_FALHOU => 'Falhou',
            self::STATUS_ARQUIVANDO => 'Arquivando',
            self::STATUS_DESARQUIVANDO => 'Desarquivando',
            default => ucfirst($this->status),
        };
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            self::STATUS_PROCESSANDO, self::STATUS_ARQUIVANDO, self::STATUS_DESARQUIVANDO => 'text-bg-primary',
            self::STATUS_CONCLUIDO => 'text-bg-success',
            self::STATUS_FALHOU => 'text-bg-danger',
            default => 'text-bg-secondary',
        };
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_arquivado' => 'boolean',
            'parametros' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }
}

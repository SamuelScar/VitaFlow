<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable(['email'])]
#[Hidden(['token_hash'])]
/**
 * Representa um convite para criar uma conta administrativa.
 */
class ConviteAdmin extends Model
{
    public const EXPIRACAO_HORAS = 48;

    protected $table = 'convites_admin';

    /**
     * Retorna o administrador responsável pelo convite.
     */
    public function convidadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'convidado_por_id');
    }

    /**
     * Renova o token e o prazo do convite.
     */
    public function renovar(User $admin): string
    {
        $token = Str::random(64);

        $this->forceFill([
            'token_hash' => hash('sha256', $token),
            'convidado_por_id' => $admin->id,
            'expira_em' => now()->addHours(self::EXPIRACAO_HORAS),
            'aceito_em' => null,
            'cancelado_em' => null,
        ])->save();

        return $token;
    }

    /**
     * Verifica se o convite ainda pode ser aceito.
     */
    public function podeSerAceito(): bool
    {
        return $this->aceito_em === null
            && $this->cancelado_em === null
            && $this->expira_em->isFuture();
    }

    public function estaExpirado(): bool
    {
        return $this->expira_em->isPast();
    }

    public function aceitar(): void
    {
        $this->forceFill(['aceito_em' => now()])->save();
    }

    public function cancelar(): void
    {
        $this->forceFill(['cancelado_em' => now()])->save();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expira_em' => 'datetime',
            'aceito_em' => 'datetime',
            'cancelado_em' => 'datetime',
        ];
    }
}

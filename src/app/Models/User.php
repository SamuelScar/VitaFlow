<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
/**
 * Representa um usuário do sistema. O campo `tipo` define o nível de acesso:
 * `admin` gerencia o sistema; `doador` participa das campanhas.
 */
class User extends Authenticatable
{
    public const TIPO_ADMIN = 'admin';
    public const TIPO_DOADOR = 'doador';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Retorna a carteirinha de doador vinculada ao usuário, ou null se ainda não emitida.
     */
    public function carteiraDoacao(): HasOne
    {
        return $this->hasOne(CarteiraDoacao::class);
    }

    /**
     * Retorna todas as campanhas criadas por este usuário administrador.
     */
    public function campanhasCriadas(): HasMany
    {
        return $this->hasMany(Campanha::class, 'criada_por_id');
    }

    /**
     * Verifica se o usuário possui perfil de administrador.
     */
    public function isAdmin(): bool
    {
        return $this->tipo === self::TIPO_ADMIN;
    }

    /**
     * Verifica se o usuário possui perfil de doador.
     */
    public function isDoador(): bool
    {
        return $this->tipo === self::TIPO_DOADOR;
    }

    /**
     * Promove o usuário para administrador. Não faz nada se já for admin (idempotente).
     */
    public function promoteToAdmin(): void
    {
        if ($this->isAdmin()) {
            return;
        }

        $this->forceFill(['tipo' => self::TIPO_ADMIN])->save();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

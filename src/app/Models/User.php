<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
class User extends Authenticatable
{
    public const TIPO_ADMIN = 'admin';
    public const TIPO_DOADOR = 'doador';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function carteiraDoacao(): HasOne
    {
        return $this->hasOne(CarteiraDoacao::class);
    }

    public function campanhasCriadas(): HasMany
    {
        return $this->hasMany(Campanha::class, 'criada_por_id');
    }

    public function isAdmin(): bool
    {
        return $this->tipo === self::TIPO_ADMIN;
    }

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

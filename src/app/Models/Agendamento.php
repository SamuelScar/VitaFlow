<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

#[Fillable(['user_id', 'campanha_id', 'data_hora', 'status'])]
/**
 * Representa o agendamento de um doador para uma campanha. Um doador não pode
 * se agendar mais de uma vez na mesma campanha (constraint única no banco).
 */
class Agendamento extends Model
{
    public const STATUS_AGENDADO = 'agendado';
    public const STATUS_CANCELADO = 'cancelado';
    public const STATUS_REALIZADO = 'realizado';
    public const STATUS_FALTOU = 'faltou';
    public const PRAZO_REGISTRO_COMPARECIMENTO_HORAS = 24;
    public const STATUS_REGISTRO_COMPARECIMENTO = [
        self::STATUS_AGENDADO,
        self::STATUS_CANCELADO,
        self::STATUS_REALIZADO,
        self::STATUS_FALTOU,
    ];

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
        return $this->status === self::STATUS_AGENDADO
            && $this->data_hora->greaterThanOrEqualTo(now());
    }

    public function podeRegistrarComparecimento(): bool
    {
        return in_array($this->status, self::STATUS_REGISTRO_COMPARECIMENTO, true)
            && $this->janelaRegistroComparecimentoIniciada()
            && ! $this->janelaRegistroComparecimentoEncerrada()
            && ! $this->possuiDoacaoRegistrada();
    }

    public function podeRegistrarDoacao(): bool
    {
        return $this->status === self::STATUS_REALIZADO
            && $this->janelaRegistroComparecimentoIniciada()
            && ! $this->possuiDoacaoRegistrada();
    }

    public function janelaRegistroComparecimentoIniciada(): bool
    {
        return now()->greaterThanOrEqualTo($this->data_hora);
    }

    public function janelaRegistroComparecimentoEncerrada(): bool
    {
        return now()->greaterThan($this->prazoRegistroComparecimento());
    }

    public function prazoRegistroComparecimento(): Carbon
    {
        return $this->data_hora->copy()->addHours(self::PRAZO_REGISTRO_COMPARECIMENTO_HORAS);
    }

    public function situacaoRegistroComparecimento(): string
    {
        if ($this->possuiDoacaoRegistrada()) {
            return 'doacao_registrada';
        }

        if ($this->status !== self::STATUS_AGENDADO && ! $this->janelaRegistroComparecimentoIniciada()) {
            return 'finalizado';
        }

        if (! $this->janelaRegistroComparecimentoIniciada()) {
            return 'aguardando_horario';
        }

        if ($this->janelaRegistroComparecimentoEncerrada()) {
            return 'prazo_encerrado';
        }

        if (! in_array($this->status, self::STATUS_REGISTRO_COMPARECIMENTO, true)) {
            return 'finalizado';
        }

        return 'disponivel';
    }

    private function possuiDoacaoRegistrada(): bool
    {
        if ($this->relationLoaded('doacao')) {
            return $this->doacao !== null;
        }

        return $this->doacao()->exists();
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

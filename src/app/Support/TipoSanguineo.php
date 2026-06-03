<?php

namespace App\Support;

/**
 * Enum dos tipos sanguíneos aceitos pelo sistema. Centraliza os valores válidos para uso em validações, migrations e exibição.
 */
enum TipoSanguineo: string
{
    case APositivo  = 'A+';
    case ANegativo  = 'A-';
    case BPositivo  = 'B+';
    case BNegativo  = 'B-';
    case ABPositivo = 'AB+';
    case ABNegativo = 'AB-';
    case OPositivo  = 'O+';
    case ONegativo  = 'O-';

    /**
     * Retorna todos os valores como array de strings.
     *
     * @return string[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

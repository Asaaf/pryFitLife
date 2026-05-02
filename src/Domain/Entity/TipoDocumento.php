<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class TipoDocumento extends AbstractEntity
{
    public const TABLE = 'tipos_documento';

    public ?int $id = null;
    public ?string $tipo_documento = null;
    public ?string $sigla = null;

    public static function tableName(): string
    {
        return self::TABLE;
    }

    public static function columns(): array
    {
        return ['id', 'tipo_documento', 'sigla'];
    }
}

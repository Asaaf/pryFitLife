<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class ClaseGrupal extends AbstractEntity
{
    public const TABLE = 'clases_grupales';

    public ?int $id = null;
    public ?string $nombre = null;
    public ?string $intensidad = null;

    public static function tableName(): string
    {
        return self::TABLE;
    }

    public static function columns(): array
    {
        return ['id', 'nombre', 'intensidad'];
    }
}

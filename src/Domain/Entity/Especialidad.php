<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class Especialidad extends AbstractEntity
{
    public const TABLE = 'especialidades';

    public ?int $id = null;
    public ?string $nombre = null;
    public ?string $descripcion = null;

    public static function tableName(): string
    {
        return self::TABLE;
    }

    public static function columns(): array
    {
        return ['id', 'nombre', 'descripcion'];
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class Sede extends AbstractEntity
{
    public const TABLE = 'sedes';

    public ?int $id = null;
    public ?string $direccion = null;
    public ?string $telefono = null;
    public ?int $ciudad_id = null;

    public static function tableName(): string
    {
        return self::TABLE;
    }

    public static function columns(): array
    {
        return ['id', 'direccion', 'telefono', 'ciudad_id'];
    }
}

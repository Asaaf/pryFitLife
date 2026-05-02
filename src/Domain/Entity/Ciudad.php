<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class Ciudad extends AbstractEntity
{
    public const TABLE = 'ciudades';

    public ?int $id = null;
    public ?string $nombre = null;
    public ?string $cod_postal = null;
    public ?int $estado_id = null;

    public static function tableName(): string
    {
        return self::TABLE;
    }

    public static function columns(): array
    {
        return ['id', 'nombre', 'cod_postal', 'estado_id'];
    }
}

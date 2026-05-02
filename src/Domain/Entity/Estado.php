<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class Estado extends AbstractEntity
{
    public const TABLE = 'estados';

    public ?int $id = null;
    public ?string $nombre = null;
    public ?string $cod_postal = null;
    public ?int $paises_id = null;

    public static function tableName(): string
    {
        return self::TABLE;
    }

    public static function columns(): array
    {
        return ['id', 'nombre', 'cod_postal', 'paises_id'];
    }
}

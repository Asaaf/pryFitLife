<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class Ejercicio extends AbstractEntity
{
    public const TABLE = 'ejercicios';

    public ?int $id = null;
    public ?string $nombre = null;
    public ?string $descripcion = null;
    public ?string $imagen = null;
    public ?string $maquina = null;

    public static function tableName(): string
    {
        return self::TABLE;
    }

    public static function columns(): array
    {
        return ['id', 'nombre', 'descripcion', 'imagen', 'maquina'];
    }
}

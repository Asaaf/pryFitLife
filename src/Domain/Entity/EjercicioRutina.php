<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class EjercicioRutina extends AbstractEntity
{
    public const TABLE = 'ejercicios_rutina';

    public ?int $id = null;
    public ?int $ciclos = null;
    public ?int $repeticiones = null;
    public ?int $id_ejercicio = null;
    public ?int $id_rutina = null;

    public static function tableName(): string
    {
        return self::TABLE;
    }

    public static function columns(): array
    {
        return ['id', 'ciclos', 'repeticiones', 'id_ejercicio', 'id_rutina'];
    }
}

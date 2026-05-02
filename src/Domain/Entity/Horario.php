<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class Horario extends AbstractEntity
{
    public const TABLE = 'horarios';

    public ?int $id = null;
    public ?int $id_clase_grupal = null;
    public ?int $id_empleado = null;
    public ?string $fecha_inicio = null;
    public ?string $fecha_fin = null;
    public ?string $hora_inicio = null;
    public ?string $hora_fin = null;

    public static function tableName(): string
    {
        return self::TABLE;
    }

    public static function columns(): array
    {
        return [
            'id',
            'id_clase_grupal',
            'id_empleado',
            'fecha_inicio',
            'fecha_fin',
            'hora_inicio',
            'hora_fin',
        ];
    }
}

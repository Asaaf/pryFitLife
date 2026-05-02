<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class Empleado extends AbstractEntity
{
    public const TABLE = 'empleados';

    public ?int $id = null;
    public ?string $identificacion = null;
    public ?string $primer_nombre = null;
    public ?string $segundo_nombre = null;
    public ?string $primer_apellido = null;
    public ?string $segundo_apellido = null;
    public ?float $salario = null;
    public ?string $fecha_ingreso = null;
    public ?int $sede_id = null;
    public ?int $especialidad_id = null;
    public ?int $tipo_documento_id = null;

    public static function tableName(): string
    {
        return self::TABLE;
    }

    public static function columns(): array
    {
        return [
            'id',
            'identificacion',
            'primer_nombre',
            'segundo_nombre',
            'primer_apellido',
            'segundo_apellido',
            'salario',
            'fecha_ingreso',
            'sede_id',
            'especialidad_id',
            'tipo_documento_id',
        ];
    }
}

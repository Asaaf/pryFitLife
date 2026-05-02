<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class Afiliado extends AbstractEntity
{
    public const TABLE = 'afiliados';

    public ?int $id = null;
    public ?string $identificacion = null;
    public ?string $primer_nombre = null;
    public ?string $segundo_nombre = null;
    public ?string $primer_apellido = null;
    public ?string $segundo_apellido = null;
    public ?string $correo_electronico = null;
    public ?string $fecha_nacimiento = null;
    public ?int $id_tipo_documento = null;
    public ?int $id_plan_nutricional = null;
    public ?int $rutina_id = null;

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
            'correo_electronico',
            'fecha_nacimiento',
            'id_tipo_documento',
            'id_plan_nutricional',
            'rutina_id',
        ];
    }
}

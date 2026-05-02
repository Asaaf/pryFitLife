<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class Seguimiento extends AbstractEntity
{
    public const TABLE = 'seguimientos';

    public ?int $id = null;
    public ?string $fecha = null;
    public ?float $peso = null;
    public ?float $altura = null;
    public ?float $imc = null;
    public ?int $id_afiliado = null;

    public static function tableName(): string
    {
        return self::TABLE;
    }

    public static function columns(): array
    {
        return ['id', 'fecha', 'peso', 'altura', 'imc', 'id_afiliado'];
    }
}

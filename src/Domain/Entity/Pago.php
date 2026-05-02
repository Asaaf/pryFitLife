<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class Pago extends AbstractEntity
{
    public const TABLE = 'pagos';

    public ?int $id = null;
    public ?int $plan_id = null;
    public ?int $afiliado_id = null;
    public ?int $nro_recibo = null;
    public ?string $fecha_pago = null;
    public ?float $valor_pagado = null;
    public ?string $metodo_pago = null;

    public static function tableName(): string
    {
        return self::TABLE;
    }

    public static function columns(): array
    {
        return [
            'id',
            'plan_id',
            'afiliado_id',
            'nro_recibo',
            'fecha_pago',
            'valor_pagado',
            'metodo_pago',
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Repository\PagoRepository;
use InvalidArgumentException;

final class PagoService extends AbstractService
{
    public function __construct(PagoRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function entityClass(): string
    {
        return \App\Domain\Entity\Pago::class;
    }

    protected function validate(array $data): void
    {
        foreach (['plan_id', 'afiliado_id', 'nro_recibo', 'fecha_pago', 'metodo_pago'] as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("El campo {$field} es requerido.");
            }
        }

        if (!isset($data['valor_pagado']) || !is_numeric($data['valor_pagado']) || (float) $data['valor_pagado'] < 0) {
            throw new InvalidArgumentException('El campo valor_pagado debe ser un numero positivo.');
        }
    }
}

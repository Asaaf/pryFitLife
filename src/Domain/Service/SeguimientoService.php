<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Repository\SeguimientoRepository;
use InvalidArgumentException;

final class SeguimientoService extends AbstractService
{
    public function __construct(SeguimientoRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function entityClass(): string
    {
        return \App\Domain\Entity\Seguimiento::class;
    }

    protected function validate(array $data): void
    {
        foreach (['fecha', 'id_afiliado'] as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("El campo {$field} es requerido.");
            }
        }

        foreach (['peso', 'altura', 'imc'] as $metric) {
            if (!isset($data[$metric]) || !is_numeric($data[$metric]) || (float) $data[$metric] <= 0) {
                throw new InvalidArgumentException("El campo {$metric} debe ser un numero positivo.");
            }
        }
    }
}

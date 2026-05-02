<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Repository\PlanRepository;
use InvalidArgumentException;

final class PlanService extends AbstractService
{
    public function __construct(PlanRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function entityClass(): string
    {
        return \App\Domain\Entity\Plan::class;
    }

    protected function validate(array $data): void
    {
        if (empty($data['nombre'])) {
            throw new InvalidArgumentException('El campo nombre es requerido.');
        }

        if (!isset($data['valor']) || !is_numeric($data['valor']) || (float) $data['valor'] < 0) {
            throw new InvalidArgumentException('El campo valor debe ser un numero positivo.');
        }
    }
}

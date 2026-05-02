<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Repository\PlanNutricionalRepository;
use InvalidArgumentException;

final class PlanNutricionalService extends AbstractService
{
    public function __construct(PlanNutricionalRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function entityClass(): string
    {
        return \App\Domain\Entity\PlanNutricional::class;
    }

    protected function validate(array $data): void
    {
        if (empty($data['nombre'])) {
            throw new InvalidArgumentException('El campo nombre es requerido.');
        }

        if (empty($data['descripcion'])) {
            throw new InvalidArgumentException('El campo descripcion es requerido.');
        }
    }
}

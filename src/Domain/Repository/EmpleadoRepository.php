<?php

declare(strict_types=1);

namespace App\Domain\Repository;

final class EmpleadoRepository extends AbstractRepository
{
    protected function entityClass(): string
    {
        return \App\Domain\Entity\Empleado::class;
    }
}

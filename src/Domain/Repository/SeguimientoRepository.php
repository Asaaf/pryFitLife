<?php

declare(strict_types=1);

namespace App\Domain\Repository;

final class SeguimientoRepository extends AbstractRepository
{
    protected function entityClass(): string
    {
        return \App\Domain\Entity\Seguimiento::class;
    }
}

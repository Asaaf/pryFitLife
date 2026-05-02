<?php

declare(strict_types=1);

namespace App\Domain\Repository;

final class PagoRepository extends AbstractRepository
{
    protected function entityClass(): string
    {
        return \App\Domain\Entity\Pago::class;
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Repository;

final class PlanRepository extends AbstractRepository
{
    protected function entityClass(): string
    {
        return \App\Domain\Entity\Plan::class;
    }
}

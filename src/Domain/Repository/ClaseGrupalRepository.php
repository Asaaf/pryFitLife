<?php 
declare(strict_types=1);

namespace App\Domain\Repository;

final class ClaseGrupalRepository extends AbstractRepository
{
    protected function entityClass(): string
    {
        return \App\Domain\Entity\ClaseGrupal::class;
    }
}

<?php
declare(strict_types=1);

namespace App\Domain\Repository;

final class CiudadRepository extends AbstractRepository
{
    protected function entityClass(): string
    {
        return \App\Domain\Entity\Ciudad::class;
    }
}

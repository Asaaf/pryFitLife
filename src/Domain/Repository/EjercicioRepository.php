<?php 
declare(strict_types=1);

namespace App\Domain\Repository;

final class EjercicioRepository extends AbstractRepository
{
    protected function entityClass(): string
    {
        return \App\Domain\Entity\Ejercicio::class;
    }
}

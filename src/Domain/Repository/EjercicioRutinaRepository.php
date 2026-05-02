<?php declare(strict_types=1);

namespace App\Domain\Repository;

final class EjercicioRutinaRepository extends AbstractRepository
{
    protected function entityClass(): string
    {
        return \App\Domain\Entity\EjercicioRutina::class;
    }
}

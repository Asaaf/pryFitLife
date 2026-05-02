<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Repository\EjercicioRepository;
use InvalidArgumentException;

final class EjercicioService extends AbstractService
{
    public function __construct(EjercicioRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function entityClass(): string
    {
        return \App\Domain\Entity\Ejercicio::class;
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

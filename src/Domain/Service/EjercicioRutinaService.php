<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Repository\EjercicioRutinaRepository;
use InvalidArgumentException;

final class EjercicioRutinaService extends AbstractService
{
    public function __construct(EjercicioRutinaRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function entityClass(): string
    {
        return \App\Domain\Entity\EjercicioRutina::class;
    }

    protected function validate(array $data): void
    {
        foreach (['id_ejercicio', 'id_rutina'] as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("El campo {$field} es requerido.");
            }
        }

        foreach (['ciclos', 'repeticiones'] as $counter) {
            if (!isset($data[$counter]) || !is_numeric($data[$counter]) || (int) $data[$counter] <= 0) {
                throw new InvalidArgumentException("El campo {$counter} debe ser un entero positivo.");
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Repository\RutinaRepository;
use InvalidArgumentException;

final class RutinaService extends AbstractService
{
    public function __construct(RutinaRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function entityClass(): string
    {
        return \App\Domain\Entity\Rutina::class;
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

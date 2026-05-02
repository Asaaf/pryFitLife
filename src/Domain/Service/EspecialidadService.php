<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Repository\EspecialidadRepository;
use InvalidArgumentException;

final class EspecialidadService extends AbstractService
{
    public function __construct(EspecialidadRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function entityClass(): string
    {
        return \App\Domain\Entity\Especialidad::class;
    }

    protected function validate(array $data): void
    {
        if (empty($data['nombre'])) {
            throw new InvalidArgumentException('El campo nombre es requerido.');
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Repository\PaisRepository;
use InvalidArgumentException;

final class PaisService extends AbstractService
{
    public function __construct(PaisRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function entityClass(): string
    {
        return \App\Domain\Entity\Pais::class;
    }

    protected function validate(array $data): void
    {
        if (empty($data['nombre'])) {
            throw new InvalidArgumentException('El campo nombre es requerido.');
        }

        if (empty($data['cod_postal'])) {
            throw new InvalidArgumentException('El campo cod_postal es requerido.');
        }
    }
}

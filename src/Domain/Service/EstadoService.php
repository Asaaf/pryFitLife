<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Repository\EstadoRepository;
use InvalidArgumentException;

final class EstadoService extends AbstractService
{
    public function __construct(EstadoRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function entityClass(): string
    {
        return \App\Domain\Entity\Estado::class;
    }

    protected function validate(array $data): void
    {
        if (empty($data['nombre'])) {
            throw new InvalidArgumentException('El campo nombre es requerido.');
        }

        if (empty($data['cod_postal'])) {
            throw new InvalidArgumentException('El campo cod_postal es requerido.');
        }

        if (empty($data['paises_id'])) {
            throw new InvalidArgumentException('El campo paises_id es requerido.');
        }
    }
}

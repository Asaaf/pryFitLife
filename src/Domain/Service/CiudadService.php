<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Repository\CiudadRepository;
use InvalidArgumentException;

final class CiudadService extends AbstractService
{
    public function __construct(CiudadRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function entityClass(): string
    {
        return \App\Domain\Entity\Ciudad::class;
    }

    protected function validate(array $data): void
    {
        if (empty($data['nombre'])) {
            throw new InvalidArgumentException('El campo nombre es requerido.');
        }

        if (empty($data['cod_postal'])) {
            throw new InvalidArgumentException('El campo cod_postal es requerido.');
        }

        if (empty($data['estado_id'])) {
            throw new InvalidArgumentException('El campo estado_id es requerido.');
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Repository\SedeRepository;
use InvalidArgumentException;

final class SedeService extends AbstractService
{
    public function __construct(SedeRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function entityClass(): string
    {
        return \App\Domain\Entity\Sede::class;
    }

    protected function validate(array $data): void
    {
        if (empty($data['direccion'])) {
            throw new InvalidArgumentException('El campo direccion es requerido.');
        }

        if (empty($data['telefono'])) {
            throw new InvalidArgumentException('El campo telefono es requerido.');
        }

        if (empty($data['ciudad_id'])) {
            throw new InvalidArgumentException('El campo ciudad_id es requerido.');
        }
    }
}

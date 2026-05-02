<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Repository\ClaseGrupalRepository;
use InvalidArgumentException;

final class ClaseGrupalService extends AbstractService
{
    // Valores permitidos segun el DDL.
    private const INTENSIDADES = ['BAJA', 'MEDIA', 'ALTA'];

    public function __construct(ClaseGrupalRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function entityClass(): string
    {
        return \App\Domain\Entity\ClaseGrupal::class;
    }

    protected function validate(array $data): void
    {
        if (empty($data['nombre'])) {
            throw new InvalidArgumentException('El campo nombre es requerido.');
        }

        if (empty($data['intensidad']) || !in_array(strtoupper((string) $data['intensidad']), self::INTENSIDADES, true)) {
            throw new InvalidArgumentException('El campo intensidad debe ser BAJA, MEDIA o ALTA.');
        }
    }
}

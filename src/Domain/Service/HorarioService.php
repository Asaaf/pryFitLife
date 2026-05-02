<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Repository\HorarioRepository;
use InvalidArgumentException;

final class HorarioService extends AbstractService
{
    public function __construct(HorarioRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function entityClass(): string
    {
        return \App\Domain\Entity\Horario::class;
    }

    protected function validate(array $data): void
    {
        foreach (['id_clase_grupal', 'id_empleado', 'fecha_inicio', 'fecha_fin', 'hora_inicio', 'hora_fin'] as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("El campo {$field} es requerido.");
            }
        }

        if (strtotime((string) $data['fecha_fin']) < strtotime((string) $data['fecha_inicio'])) {
            throw new InvalidArgumentException('La fecha_fin no puede ser anterior a fecha_inicio.');
        }

        if ($data['hora_fin'] <= $data['hora_inicio']) {
            throw new InvalidArgumentException('La hora_fin debe ser posterior a hora_inicio.');
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Repository\EmpleadoRepository;
use InvalidArgumentException;

final class EmpleadoService extends AbstractService
{
    public function __construct(EmpleadoRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function entityClass(): string
    {
        return \App\Domain\Entity\Empleado::class;
    }

    protected function validate(array $data): void
    {
        foreach (['identificacion', 'primer_nombre', 'primer_apellido', 'fecha_ingreso', 'sede_id', 'especialidad_id', 'tipo_documento_id'] as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("El campo {$field} es requerido.");
            }
        }

        if (!isset($data['salario']) || !is_numeric($data['salario']) || (float) $data['salario'] < 0) {
            throw new InvalidArgumentException('El campo salario debe ser un numero positivo.');
        }
    }
}

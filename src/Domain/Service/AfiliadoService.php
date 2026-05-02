<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Repository\AfiliadoRepository;
use InvalidArgumentException;

final class AfiliadoService extends AbstractService
{
    public function __construct(AfiliadoRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function entityClass(): string
    {
        return \App\Domain\Entity\Afiliado::class;
    }

    protected function validate(array $data): void
    {
        foreach (['identificacion', 'primer_nombre', 'primer_apellido', 'fecha_nacimiento', 'id_tipo_documento', 'id_plan_nutricional', 'rutina_id'] as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("El campo {$field} es requerido.");
            }
        }

        if (empty($data['correo_electronico']) || !filter_var($data['correo_electronico'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('El campo correo_electronico debe ser un email valido.');
        }
    }
}

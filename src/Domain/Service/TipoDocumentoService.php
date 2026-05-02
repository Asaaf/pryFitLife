<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Repository\TipoDocumentoRepository;
use InvalidArgumentException;

final class TipoDocumentoService extends AbstractService
{
    public function __construct(TipoDocumentoRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function entityClass(): string
    {
        return \App\Domain\Entity\TipoDocumento::class;
    }

    protected function validate(array $data): void
    {
        if (empty($data['tipo_documento'])) {
            throw new InvalidArgumentException('El campo tipo_documento es requerido.');
        }
    }
}

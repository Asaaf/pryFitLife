<?php

declare(strict_types=1);

namespace App\Domain\Repository;

final class TipoDocumentoRepository extends AbstractRepository
{
    protected function entityClass(): string
    {
        return \App\Domain\Entity\TipoDocumento::class;
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\EntityInterface;
use App\Domain\Repository\AbstractRepository;
use InvalidArgumentException;

/**
 * @template TEntity of EntityInterface
 * @implements ServiceInterface<TEntity>
 */
abstract class AbstractService implements ServiceInterface
{
    /**
     * @param AbstractRepository<TEntity> $repository
     */
    public function __construct(
        protected readonly AbstractRepository $repository
    ) {
    }

    /**
     * @return list<TEntity>
     */
    public function getAll(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @return array{items:list<TEntity>, total:int, page:int, per_page:int, total_pages:int}
     */
    public function getPage(int $page, int $perPage, ?string $query = null): array
    {
        if ($page <= 0) {
            throw new InvalidArgumentException('El parametro page debe ser un entero positivo.');
        }

        if ($perPage <= 0) {
            throw new InvalidArgumentException('El parametro per_page debe ser un entero positivo.');
        }

        return $this->repository->findPage($page, $perPage, $query);
    }

    /**
     * @return TEntity|null
     */
    public function getById(int $id): ?EntityInterface
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('El id debe ser un entero positivo.');
        }

        return $this->repository->findById($id);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return TEntity
     */
    public function create(array $data): EntityInterface
    {
        $this->validate($data);

        $entityClass = $this->entityClass();
        $entity = $entityClass::fromArray($data);

        return $this->repository->create($entity);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('El id debe ser un entero positivo.');
        }

        $entityClass = $this->entityClass();
        $entity = $entityClass::fromArray($data);

        return $this->repository->update($id, $entity);
    }

    public function delete(int $id): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('El id debe ser un entero positivo.');
        }

        return $this->repository->delete($id);
    }

    /**
     * Reglas de validacion propias de cada entidad.
     * Sobrescribir en el servicio concreto cuando se necesiten.
     *
     * @param array<string, mixed> $data
     */
    protected function validate(array $data): void
    {
        // Sin restricciones en la base; cada servicio concreto afina su validacion.
    }

    /**
     * @return class-string<TEntity>
     */
    abstract protected function entityClass(): string;
}

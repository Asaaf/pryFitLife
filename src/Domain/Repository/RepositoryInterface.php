<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\EntityInterface;

/**
 * @template TEntity of EntityInterface
 */
interface RepositoryInterface
{
    /**
     * @return list<TEntity>
     */
    public function findAll(): array;

    /**
     * @return TEntity|null
     */
    public function findById(int $id): ?EntityInterface;

    /**
     * @param TEntity $entity
     *
     * @return TEntity
     */
    public function create(EntityInterface $entity): EntityInterface;

    /**
     * @param TEntity $entity
     */
    public function update(int $id, EntityInterface $entity): bool;

    public function delete(int $id): bool;
}

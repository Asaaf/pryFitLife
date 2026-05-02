<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\EntityInterface;

/**
 * @template TEntity of EntityInterface
 */
interface ServiceInterface
{
    /**
     * @return list<TEntity>
     */
    public function getAll(array $filters = []): array;

    /**
     * @return array{items:list<TEntity>, total:int, page:int, per_page:int, total_pages:int}
     */
    public function getPage(int $page, int $perPage, ?string $query = null, array $filters = []): array;

    /**
     * @return TEntity|null
     */
    public function getById(int $id): ?EntityInterface;

    /**
     * @param array<string, mixed> $data
     *
     * @return TEntity
     */
    public function create(array $data): EntityInterface;

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}

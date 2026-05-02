<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Database\Connection;
use App\Domain\Entity\EntityInterface;
use InvalidArgumentException;
use PDO;

/**
 * @template TEntity of EntityInterface
 * @implements RepositoryInterface<TEntity>
 */
abstract class AbstractRepository implements RepositoryInterface
{
    private PDO $connection;

    public function __construct(?PDO $connection = null)
    {
        $this->connection = $connection ?? Connection::getInstance();
    }

    /**
     * @return class-string<TEntity>
     */
    abstract protected function entityClass(): string;

    /**
     * @return list<TEntity>
     */
    public function findAll(): array
    {
        $sql = sprintf('SELECT * FROM %s', $this->tableName());
        $statement = $this->connection->query($sql);

        /** @var list<array<string, mixed>> $rows */
        $rows = $statement->fetchAll();

        $entityClass = $this->entityClass();

        return array_map(static fn (array $row): EntityInterface => $entityClass::fromArray($row), $rows);
    }

    /**
     * @return TEntity|null
     */
    public function findById(int $id): ?EntityInterface
    {
        $sql = sprintf('SELECT * FROM %s WHERE id = :id LIMIT 1', $this->tableName());
        $statement = $this->connection->prepare($sql);
        $statement->execute(['id' => $id]);

        /** @var array<string, mixed>|false $row */
        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        $entityClass = $this->entityClass();

        return $entityClass::fromArray($row);
    }

    /**
     * @param TEntity $entity
     *
     * @return TEntity
     */
    public function create(EntityInterface $entity): EntityInterface
    {
        $payload = $this->extractPayload($entity, includeId: true, includeNullValues: false);

        if ($payload === []) {
            throw new InvalidArgumentException('No hay datos para crear la entidad.');
        }

        $columns = array_keys($payload);
        $placeholders = array_map(static fn (string $column): string => ':' . $column, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->tableName(),
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $statement = $this->connection->prepare($sql);
        $statement->execute($payload);

        if (!array_key_exists('id', $payload)) {
            $lastInsertId = (int) $this->connection->lastInsertId();
            if ($lastInsertId > 0) {
                $payload['id'] = $lastInsertId;
            }
        }

        $entityClass = $this->entityClass();

        /** @var TEntity */
        return $entityClass::fromArray(array_merge($entity->toArray(), $payload));
    }

    /**
     * @param TEntity $entity
     */
    public function update(int $id, EntityInterface $entity): bool
    {
        $payload = $this->extractPayload($entity, includeId: false, includeNullValues: true);

        if ($payload === []) {
            return false;
        }

        $assignments = array_map(static fn (string $column): string => sprintf('%s = :%s', $column, $column), array_keys($payload));

        $sql = sprintf(
            'UPDATE %s SET %s WHERE id = :id',
            $this->tableName(),
            implode(', ', $assignments)
        );

        $statement = $this->connection->prepare($sql);
        $statement->execute(array_merge($payload, ['id' => $id]));

        return $statement->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $sql = sprintf('DELETE FROM %s WHERE id = :id', $this->tableName());
        $statement = $this->connection->prepare($sql);
        $statement->execute(['id' => $id]);

        return $statement->rowCount() > 0;
    }

    /**
     * @param TEntity $entity
     * @return array<string, mixed>
     */
    protected function extractPayload(EntityInterface $entity, bool $includeId, bool $includeNullValues): array
    {
        $payload = [];

        foreach ($entity::columns() as $column) {
            if (!$includeId && $column === 'id') {
                continue;
            }

            $value = $entity->toArray()[$column] ?? null;

            if ($value === null && !$includeNullValues) {
                continue;
            }

            $payload[$column] = $value;
        }

        return $payload;
    }

    protected function tableName(): string
    {
        $entityClass = $this->entityClass();

        return $entityClass::tableName();
    }
}

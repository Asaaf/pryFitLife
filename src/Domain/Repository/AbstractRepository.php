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
     * @return array{items:list<TEntity>, total:int, page:int, per_page:int, total_pages:int}
     */
    public function findPage(int $page, int $perPage, ?string $query = null): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        [$whereSql, $params] = $this->buildSearchClause($query);

        $countSql = sprintf('SELECT COUNT(*) FROM %s%s', $this->tableName(), $whereSql);
        $countStatement = $this->connection->prepare($countSql);
        $countStatement->execute($params);
        $total = (int) $countStatement->fetchColumn();

        $sql = sprintf(
            'SELECT * FROM %s%s ORDER BY id ASC LIMIT :limit OFFSET :offset',
            $this->tableName(),
            $whereSql,
        );

        $statement = $this->connection->prepare($sql);

        foreach ($params as $name => $value) {
            $statement->bindValue(':' . $name, $value, PDO::PARAM_STR);
        }

        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        /** @var list<array<string, mixed>> $rows */
        $rows = $statement->fetchAll();
        $entityClass = $this->entityClass();
        $items = array_map(static fn (array $row): EntityInterface => $entityClass::fromArray($row), $rows);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => max(1, (int) ceil($total / $perPage)),
        ];
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

    /**
     * @return array{0:string, 1:array<string, string>}
     */
    protected function buildSearchClause(?string $query): array
    {
        $query = trim((string) $query);

        if ($query === '') {
            return ['', []];
        }

        $conditions = [];
        $params = [];

        foreach ($this->searchableColumns() as $index => $column) {
            $param = 'search_' . $index;
            $conditions[] = sprintf('CAST(%s AS CHAR) LIKE :%s', $column, $param);
            $params[$param] = '%' . $query . '%';
        }

        if ($conditions === []) {
            return ['', []];
        }

        return [' WHERE ' . implode(' OR ', $conditions), $params];
    }

    /**
     * @return list<string>
     */
    protected function searchableColumns(): array
    {
        $entityClass = $this->entityClass();

        return $entityClass::columns();
    }
}

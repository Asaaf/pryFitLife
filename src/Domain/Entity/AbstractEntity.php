<?php

declare(strict_types=1);

namespace App\Domain\Entity;

abstract class AbstractEntity implements EntityInterface
{
    public static function fromArray(array $data): static
    {
        $entity = new static();

        foreach (static::columns() as $column) {
            if (array_key_exists($column, $data)) {
                $entity->{$column} = $data[$column];
            }
        }

        return $entity;
    }

    public function toArray(): array
    {
        $payload = [];

        foreach (static::columns() as $column) {
            $payload[$column] = $this->{$column} ?? null;
        }

        return $payload;
    }
}

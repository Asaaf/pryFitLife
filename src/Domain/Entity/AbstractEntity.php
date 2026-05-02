<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use ReflectionNamedType;
use ReflectionProperty;

abstract class AbstractEntity implements EntityInterface
{
    /** @var array<class-string, array<string, string|null>> */
    private static array $propertyTypeCache = [];

    public static function fromArray(array $data): static
    {
        $entity = new static();

        foreach (static::columns() as $column) {
            if (array_key_exists($column, $data)) {
                $entity->{$column} = self::coerceValue($entity, $column, $data[$column]);
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

    private static function coerceValue(self $entity, string $property, mixed $value): mixed
    {
        $typeName = self::propertyType($entity, $property);

        if ($value === null) {
            return null;
        }

        if ($value === '' && $typeName !== 'string') {
            return null;
        }

        return match ($typeName) {
            'int' => (int) $value,
            'float' => (float) $value,
            'bool' => filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? (bool) $value,
            'string' => (string) $value,
            default => $value,
        };
    }

    private static function propertyType(self $entity, string $property): ?string
    {
        $class = $entity::class;

        if (!isset(self::$propertyTypeCache[$class])) {
            self::$propertyTypeCache[$class] = [];
        }

        if (array_key_exists($property, self::$propertyTypeCache[$class])) {
            return self::$propertyTypeCache[$class][$property];
        }

        if (!property_exists($entity, $property)) {
            self::$propertyTypeCache[$class][$property] = null;
            return null;
        }

        $reflectionProperty = new ReflectionProperty($entity, $property);
        $type = $reflectionProperty->getType();

        if (!$type instanceof ReflectionNamedType) {
            self::$propertyTypeCache[$class][$property] = null;
            return null;
        }

        self::$propertyTypeCache[$class][$property] = $type->getName();

        return self::$propertyTypeCache[$class][$property];
    }
}

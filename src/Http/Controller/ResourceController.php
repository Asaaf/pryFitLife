<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Domain\Service\ServiceInterface;
use App\Domain\Entity\EntityInterface;
use InvalidArgumentException;

/**
 * Controlador REST generico. Recibe un servicio concreto y responde los cinco
 * endpoints estandar de un recurso (index, show, store, replace, destroy).
 *
 * Todas las respuestas siguen el envelope { data, message, errors }.
 */
final class ResourceController
{
    public function __construct(private readonly ServiceInterface $service) {}

    // GET /resource
    public function index(array $queryParams = []): array
    {
        $filters = $this->extractFilters($queryParams);

        $page = $this->readPositiveInt($queryParams['page'] ?? null);
        $perPage = $this->readPositiveInt($queryParams['per_page'] ?? null);
        $query = isset($queryParams['q']) ? trim((string) $queryParams['q']) : null;

        if ($page === null && $perPage === null && ($query === null || $query === '')) {
            $items = $this->service->getAll($filters);

            return [
                'status' => 200,
                'body' => [
                    'data' => array_map(
                        static fn(EntityInterface $e): array => $e->toArray(),
                        $items,
                    ),
                ],
            ];
        }

        if (($page === null) !== ($perPage === null)) {
            return [
                'status' => 400,
                'body' => ['message' => 'Los parametros page y per_page deben enviarse juntos.'],
            ];
        }

        try {
            $result = $this->service->getPage($page ?? 1, $perPage ?? 10, $query, $filters);
        } catch (InvalidArgumentException $e) {
            return [
                'status' => 400,
                'body' => ['message' => $e->getMessage()],
            ];
        }

        return [
            'status' => 200,
            'body' => [
                'data' => array_map(
                    static fn(EntityInterface $e): array => $e->toArray(),
                    $result['items'],
                ),
                'meta' => [
                    'total' => $result['total'],
                    'page' => $result['page'],
                    'per_page' => $result['per_page'],
                    'total_pages' => $result['total_pages'],
                    'query' => $query,
                ],
            ],
        ];
    }

    // GET /resource/{id}
    public function show(int $id): array
    {
        $item = $this->service->getById($id);

        if ($item === null) {
            return [
                'status' => 404,
                'body' => ['message' => 'Recurso no encontrado.'],
            ];
        }

        return [
            'status' => 200,
            'body' => ['data' => $item->toArray()],
        ];
    }

    // POST /resource
    public function store(array $data): array
    {
        try {
            $created = $this->service->create($data);

            return [
                'status' => 201,
                'body' => [
                    'message' => 'Recurso creado exitosamente.',
                    'data' => $created->toArray(),
                ],
            ];
        } catch (InvalidArgumentException $e) {
            return [
                'status' => 422,
                'body' => ['errors' => [$e->getMessage()]],
            ];
        }
    }

    // PUT /resource/{id}
    public function replace(int $id, array $data): array
    {
        try {
            $updated = $this->service->update($id, $data);

            if (!$updated) {
                return [
                    'status' => 404,
                    'body' => ['message' => 'Recurso no encontrado.'],
                ];
            }

            return [
                'status' => 200,
                'body' => ['message' => 'Recurso actualizado exitosamente.'],
            ];
        } catch (InvalidArgumentException $e) {
            return [
                'status' => 422,
                'body' => ['errors' => [$e->getMessage()]],
            ];
        }
    }

    // DELETE /resource/{id}
    public function destroy(int $id): array
    {
        $deleted = $this->service->delete($id);

        if (!$deleted) {
            return [
                'status' => 404,
                'body' => ['message' => 'Recurso no encontrado.'],
            ];
        }

        return [
            'status' => 200,
            'body' => ['message' => 'Recurso eliminado exitosamente.'],
        ];
    }

    private function readPositiveInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value) || !ctype_digit((string) $value)) {
            throw new InvalidArgumentException('Los parametros de paginacion deben ser enteros positivos.');
        }

        $number = (int) $value;

        return $number > 0 ? $number : null;
    }

    /**
     * @param array<string, mixed> $queryParams
     * @return array<string, mixed>
     */
    private function extractFilters(array $queryParams): array
    {
        unset($queryParams['page'], $queryParams['per_page'], $queryParams['q']);

        return $queryParams;
    }
}

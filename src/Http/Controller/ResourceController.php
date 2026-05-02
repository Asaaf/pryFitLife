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
    public function index(): array
    {
        $items = $this->service->getAll();

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
}

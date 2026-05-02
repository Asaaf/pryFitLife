<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

/**
 * Convierte excepciones en respuestas de API consistentes.
 */
final class ErrorHandler
{
    /**
     * Mapea cualquier excepcion a una estructura JSON con estado HTTP 500.
     *
     * @return array{status:int, body:array<string, mixed>}
     */
    public static function toApiResponse(Throwable $exception, bool $isDebug): array
    {
        // En desarrollo muestra mensaje real; en produccion evita filtrar detalles internos.
        $message = $isDebug
            ? $exception->getMessage()
            : 'Internal Server Error';

        // Cuerpo base comun para cualquier error no controlado.
        $body = ['message' => $message];

        // Informacion adicional util para depuracion local.
        if ($isDebug) {
            $body['type'] = $exception::class;
            $body['trace'] = $exception->getTraceAsString();
        }

        // Respuesta estandar para fallos internos.
        return [
            'status' => 500,
            'body' => $body,
        ];
    }
}

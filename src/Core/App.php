<?php

declare(strict_types=1);

namespace App\Core;

use App\Config\Config;
use App\Database\Connection;
use Throwable;

/**
 * Nucleo minimo de aplicacion: enruta solicitudes y estandariza respuestas.
 */
final class App
{
    /**
     * Procesa una solicitud HTTP y devuelve una respuesta estructurada para la capa de salida.
     *
     * @return array{status:int, body:array<string, mixed>}
     */
    public function handleRequest(string $method, string $uri): array
    {
        // Extrae solo la ruta para ignorar query params en el enrutamiento.
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        try {
            // Ruta de salud para comprobar disponibilidad de servicio y base de datos.
            if ($method === 'GET' && $path === '/health') {
                return $this->healthCheck();
            }

            // Respuesta por defecto cuando no existe coincidencia de ruta/metodo.
            return [
                'status' => 404,
                'body' => ['message' => 'Route not found'],
            ];
        } catch (Throwable $exception) {
            // Centraliza el formato de error y evita exponer detalles si debug esta desactivado.
            return ErrorHandler::toApiResponse($exception, Config::isDebug());
        }
    }

    /**
     * Ejecuta una verificacion basica de conectividad a MySQL.
     *
     * @return array{status:int, body:array<string, mixed>}
     */
    private function healthCheck(): array
    {
        // Obtiene conexion activa y consulta la version para validar comunicacion real.
        $pdo = Connection::getInstance();
        $dbVersion = (string) $pdo->query('SELECT VERSION()')->fetchColumn();

        // Respuesta de diagnostico simple para monitoreo o pruebas iniciales.
        return [
            'status' => 200,
            'body' => [
                'service' => 'ok',
                'database' => 'ok',
                'mysql_version' => $dbVersion,
            ],
        ];
    }
}

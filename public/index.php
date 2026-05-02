<?php

declare(strict_types=1);

use App\Core\App;

// Carga el bootstrap global (autoload, variables de entorno y ajustes base).
require dirname(__DIR__) . '/src/bootstrap.php';

// Instancia la aplicacion y delega el manejo de la solicitud HTTP actual.
$app = new App();
$response = $app->handleRequest($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');

// Establece el codigo de estado HTTP devuelto por la capa de aplicacion.
http_response_code($response['status']);
// Fuerza salida JSON UTF-8 para mantener una API consistente.
header('Content-Type: application/json; charset=utf-8');

// Serializa el cuerpo de respuesta en JSON legible para pruebas locales.
echo json_encode($response['body'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

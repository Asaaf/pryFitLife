<?php

/**
 * Router para el servidor integrado de PHP.
 *
 * Uso:
 *   php -S localhost:8000 -t public router.php
 *
 * Comportamiento:
 *   - Si el archivo fisico existe en public/ (CSS, JS, imagenes, etc.) → se sirve directamente.
 *   - Cualquier otra ruta → se delega a public/index.php (front controller).
 */

declare(strict_types=1);

$requestUri  = $_SERVER['REQUEST_URI'] ?? '/';
$requestPath = parse_url($requestUri, PHP_URL_PATH) ?? '/';

// Ruta absoluta al archivo dentro de public/.
$publicFile = __DIR__ . '/public' . $requestPath;

// Redirige la raiz al panel administrativo.
if ($requestPath === '/') {
    header('Location: /app.html', true, 302);
    exit;
}

if (file_exists($publicFile) && !is_dir($publicFile)) {
    // Devuelve false para que el servidor sirva el archivo estatico sin modificaciones.
    return false;
}

// Todo lo demas pasa por el front controller.
require __DIR__ . '/public/index.php';

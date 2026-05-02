<?php

declare(strict_types=1);

use Dotenv\Dotenv;

// Registra el autoload de Composer para cargar clases y dependencias externas.
require dirname(__DIR__) . '/vendor/autoload.php';

// Raiz del proyecto para resolver rutas de forma centralizada.
$rootPath = dirname(__DIR__);

// Si existe .env, carga variables de entorno sin sobrescribir variables ya definidas.
if (file_exists($rootPath . '/.env')) {
    Dotenv::createImmutable($rootPath)->safeLoad();
}

// Define codificacion interna para manejo correcto de texto multibyte.
mb_internal_encoding('UTF-8');
// Zona horaria por defecto para fechas consistentes en toda la aplicacion.
date_default_timezone_set('UTC');

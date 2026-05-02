<?php

declare(strict_types=1);

namespace App\Config;

/**
 * Utilidad estatica para leer configuracion del entorno.
 */
final class Config
{
    /**
     * Evita instancias accidentales: esta clase se usa solo con metodos estaticos.
     */
    private function __construct()
    {
    }

    /**
     * Obtiene una variable desde $_ENV, $_SERVER o getenv con fallback opcional.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        // Prioriza variables cargadas por dotenv, luego servidor y por ultimo entorno del sistema.
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        // Si no hay valor util, devuelve el valor por defecto.
        if ($value === false || $value === null || $value === '') {
            return $default;
        }

        // Fuerza retorno como string para mantener una API predecible.
        return (string) $value;
    }

    /**
     * Interpreta APP_DEBUG como booleano para activar o no respuestas detalladas.
     */
    public static function isDebug(): bool
    {
        return filter_var(self::get('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOL);
    }
}

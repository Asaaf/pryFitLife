<?php

declare(strict_types=1);

namespace App\Config;

/**
 * Value object con la configuracion necesaria para conectarse a MySQL.
 */
final class DatabaseConfig
{
    /**
     * Constructor promovido con propiedades inmutables para evitar cambios en runtime.
     */
    public function __construct(
        public readonly string $host,
        public readonly int $port,
        public readonly string $database,
        public readonly string $username,
        public readonly string $password,
        public readonly string $charset,
    ) {
    }

    /**
     * Construye la configuracion leyendo variables de entorno con valores por defecto seguros.
     */
    public static function fromEnv(): self
    {
        return new self(
            host: Config::get('DB_HOST', '127.0.0.1'),
            port: (int) Config::get('DB_PORT', '3306'),
            database: Config::get('DB_DATABASE', ''),
            username: Config::get('DB_USERNAME', ''),
            password: Config::get('DB_PASSWORD', ''),
            charset: Config::get('DB_CHARSET', 'utf8mb4'),
        );
    }

    /**
     * Valida que los datos minimos para la conexion esten presentes.
     */
    public function validate(): void
    {
        // Sin nombre de base o usuario no es posible abrir la conexion.
        if ($this->database === '' || $this->username === '') {
            throw new \InvalidArgumentException('Faltan variables DB_DATABASE o DB_USERNAME en el entorno.');
        }
    }
}

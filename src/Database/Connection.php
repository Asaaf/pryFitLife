<?php

declare(strict_types=1);

namespace App\Database;

use App\Config\DatabaseConfig;
use PDO;
use PDOException;

/**
 * Gestiona una unica instancia PDO para reutilizar la conexion a MySQL.
 */
final class Connection
{
    // Singleton de PDO para no abrir conexiones repetidas en la misma ejecucion.
    private static ?PDO $pdo = null;

    /**
     * Devuelve una conexion PDO lista para usar con configuracion segura.
     */
    public static function getInstance(): PDO
    {
        // Reutiliza la conexion si ya fue creada previamente.
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        // Carga y valida parametros requeridos desde entorno.
        $config = DatabaseConfig::fromEnv();
        $config->validate();

        // DSN de MySQL con charset explicito para evitar problemas de codificacion.
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config->host,
            $config->port,
            $config->database,
            $config->charset,
        );

        try {
            // Crea PDO con buenas practicas: excepciones, fetch asociativo y prepares reales.
            self::$pdo = new PDO(
                $dsn,
                $config->username,
                $config->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $exception) {
            // Envuelve el error original con un mensaje de dominio mas claro para la API.
            throw new PDOException('No se pudo conectar a la base de datos MySQL.', (int) $exception->getCode(), $exception);
        }

        return self::$pdo;
    }
}

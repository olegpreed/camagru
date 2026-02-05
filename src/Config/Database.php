<?php

namespace Config;

use PDO;
use PDOException;

/**
 * Database Connection Class
 * Handles PDO connection to MySQL database
 */
class Database
{
    private static ?PDO $instance = null;
    private static array $config = [];

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct()
    {
    }

    /**
     * Get database configuration from environment variables
     */
    private static function loadConfig(): array
    {
        if (empty(self::$config)) {
            // Load config from environment (Docker Compose passes .env vars to container)
            self::$config = [
                'host' => getenv('DB_HOST') ?: 'db',
                'dbname' => getenv('DB_NAME') ?: 'camagru_db',
                'username' => getenv('DB_USER') ?: 'camagru_user',
                'password' => getenv('DB_PASS') ?: 'camagru_password',
                'charset' => 'utf8mb4',
            ];
        }

        return self::$config;
    }

    /**
     * Get PDO instance (Singleton pattern)
     * 
     * @return PDO
     * @throws PDOException
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = self::loadConfig();

            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                $config['host'],
                $config['dbname'],
                $config['charset']
            );

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                self::$instance = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $options
                );
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw new PDOException("Database connection failed. Please check your configuration.");
            }
        }

        return self::$instance;
    }

    /**
     * Reset the connection (useful for testing)
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    /**
     * Prevent cloning
     */
    private function __clone()
    {
    }

    /**
     * Prevent unserialization
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}
<?php

namespace App\Utils;

use PDO;
use PDOException;

/**
 * Database Singleton Class
 *
 * Manages MySQL database connection using PDO with prepared statements.
 * Implements singleton pattern to ensure single database connection instance.
 *
 * @package App\Utils
 */
class Database
{
    private static ?self $instance = null;
    private PDO $pdo;

    /**
     * Private constructor - prevents direct instantiation
     *
     * @throws PDOException If connection fails
     */
    private function __construct()
    {
        $this->connect();
    }

    /**
     * Get singleton instance of Database
     *
     * @return PDO The PDO database connection object
     * @throws PDOException If connection fails
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }

    /**
     * Reset singleton instance (for testing purposes)
     */
    public static function resetInstance(): void
    {
        self::$instance = null;
    }

    /**
     * Establish database connection using credentials from .env
     *
     * @throws PDOException If connection or configuration fails
     */
    private function connect(): void
    {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $port = $_ENV['DB_PORT'] ?? 3306;
        $dbName = $_ENV['DB_NAME'] ?? 'dre_ceo_dev';
        $user = $_ENV['DB_USER'] ?? 'root';
        $password = $_ENV['DB_PASSWORD'] ?? '';
        $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

        $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset={$charset}";

        try {
            $this->pdo = new PDO(
                $dsn,
                $user,
                $password,
                [
                    // Throw exceptions on errors
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    // Return results as associative arrays
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    // Emulate prepared statements for better compatibility
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            throw new PDOException(
                "Database connection failed: " . $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        }
    }

    /**
     * Execute a prepared statement with bound parameters
     *
     * @param string $sql SQL query with ? or :param placeholders
     * @param array $params Bind parameters (indexed or associative array)
     * @return \PDOStatement The executed statement
     * @throws PDOException If query execution fails
     */
    public static function execute(string $sql, array $params = []): \PDOStatement
    {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch single row as associative array
     *
     * @param string $sql SQL query
     * @param array $params Bind parameters
     * @return array|null Single row or null if not found
     */
    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = self::execute($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Fetch all rows as array of associative arrays
     *
     * @param string $sql SQL query
     * @param array $params Bind parameters
     * @return array Array of rows
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        $stmt = self::execute($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch single column value
     *
     * @param string $sql SQL query
     * @param array $params Bind parameters
     * @return mixed Single value or null
     */
    public static function fetchColumn(string $sql, array $params = []): mixed
    {
        $stmt = self::execute($sql, $params);
        return $stmt->fetchColumn();
    }

    /**
     * Get last inserted ID
     *
     * @return string|false The ID or false if not available
     */
    public static function lastInsertId(): string|false
    {
        return self::getInstance()->lastInsertId();
    }

    /**
     * Get number of affected rows
     *
     * @param \PDOStatement $stmt The statement to check
     * @return int Number of affected rows
     */
    public static function rowCount(\PDOStatement $stmt): int
    {
        return $stmt->rowCount();
    }

    /**
     * Begin database transaction
     *
     * @return bool True on success
     */
    public static function beginTransaction(): bool
    {
        return self::getInstance()->beginTransaction();
    }

    /**
     * Commit database transaction
     *
     * @return bool True on success
     */
    public static function commit(): bool
    {
        return self::getInstance()->commit();
    }

    /**
     * Rollback database transaction
     *
     * @return bool True on success
     */
    public static function rollback(): bool
    {
        return self::getInstance()->rollback();
    }

    /**
     * Check if connected to database
     *
     * @return bool True if connected
     */
    public static function isConnected(): bool
    {
        try {
            self::getInstance()->getAttribute(PDO::ATTR_CONNECTION_STATUS);
            return true;
        } catch (PDOException) {
            return false;
        }
    }

    /**
     * Test database connection with simple query
     *
     * @return bool True if connection and query successful
     */
    public static function testConnection(): bool
    {
        try {
            $result = self::fetchColumn("SELECT 1");
            return $result === '1' || $result === 1;
        } catch (PDOException) {
            return false;
        }
    }

    // Prevent cloning
    private function __clone()
    {
    }

    // Prevent unserialization
    public function __wakeup()
    {
        throw new PDOException("Cannot unserialize singleton");
    }
}

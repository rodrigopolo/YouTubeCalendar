<?php

/**
 * Database — PDO singleton wrapper.
 *
 * Reads DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD constants
 * from the application config. Provides thin helpers for SELECT
 * and write (INSERT / UPDATE / DELETE) queries.
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    /** Singleton — use Database::getInstance() */
    private function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            DB_HOST,
            DB_NAME
        );

        $this->pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    /**
     * Returns the shared Database instance, creating it on first call.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Executes a SELECT query and returns all matching rows.
     *
     * @param  string  $sql    SQL statement with optional ? placeholders
     * @param  array<int|string, mixed>  $params  Values to bind
     * @return array<int, array<string, mixed>>
     */
    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Executes an INSERT, UPDATE, or DELETE statement.
     *
     * @param  string  $sql    SQL statement with optional ? placeholders
     * @param  array<int|string, mixed>  $params  Values to bind
     */
    public function execute(string $sql, array $params = []): void
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }
}

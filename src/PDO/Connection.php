<?php

namespace Atheo\Orm\PDO;

use Exception;
use PDO;
use PDOException;
use PDOStatement;
use assert;

class Connection
{

    /**
     * The underlying PDO Connection
     * @var PDO
     */
    protected $connection;

    /**
     * Create PDO connection instance =
     * 
     * @param PDO $connection
     * @return void
     */
    public function __construct(PDO $connection = null)
    {
        $this->connection = $connection;
    }

    /**
     * Execute an SQL statement
     * @param string $statement
     * @return int
     */
    public function exec(string $statement): int
    {
        try {
            $result = $this->connection->exec($statement);

            assert($result !== false);

            return $result;
        } catch (PDOException $exception) {
            throw $exception;
        }
    }

    /**
     * Prepare a new SQL statement
     * @param string $sql
     * @throws $exception
     */
    public function prepare(string $sql)
    {
        try {
            return $this->createStatement(
                $this->connection->prepare($sql)
            );
        } catch (PDOException $exception) {
            throw $exception;
        }
    }

    public function query(string $sql)
    {
        try {
            $statement = $this->connection->query($sql);

            assert($statement instanceof PDOStatement);
        } catch (PDOException $exception) {
            throw $exception;
        }
    }

    /**
     * Get the last insert ID.
     *
     * @param string|null $name
     * @return mixed
     */
    public function lastInsertId($name = null)
    {
        try {
            if ($name === null) {
                return $this->connection->lastInsertId();
            }

            return $this->connection->lastInsertId($name);
        } catch (PDOException $exception) {
            throw $exception;
        }
    }

    /**
     * Create a new statement instance.
     *
     * @param  PDOStatement  $stmt
     */
    protected function createStatement(PDOStatement $stmt)
    {
    }

    /**
     * Begin a new database transaction.
     *
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit a database transaction.
     *
     * @return bool
     */
    public function commit()
    {
        return $this->connection->commit();
    }

    /**
     * Rollback a database transaction.
     *
     * @return bool
     */
    public function rollBack()
    {
        return $this->connection->rollBack();
    }

    /**
     * Wrap quotes around the given input.
     *
     * @param string $input
     * @param string $type
     * @return string
     */
    public function quote($input, $type = /*ParameterType::STRING*/ null)
    {
        return $this->connection->quote($input, $type);
    }

    /**
     * Get the server version for the connection.
     *
     * @return string
     */
    public function getServerVersion()
    {
        return $this->connection->getAttribute(PDO::ATTR_SERVER_VERSION);
    }

    /**
     * Get the wrapped PDO connection.
     *
     * @return PDO
     */
    public function getWrappedConnection(): PDO
    {
        return $this->connection;
    }
}

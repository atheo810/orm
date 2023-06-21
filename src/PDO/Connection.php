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
     * instance PDO
     * @var PDO $pdo
     */
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param string $query
     * @param array $params
     */
    public function executeQuery(string $query, array $params = []):PDOStatement
    {
        try {
            $statement = $this->pdo->prepare($query);
            $statement->execute($params);
            return $statement;
        } catch (PDOException $exception) {
            throw $exception;
        }
    }
}

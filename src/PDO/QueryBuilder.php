<?php

namespace Atheo\Orm\PDO;

use PDO;
use PDOException;
use PDOStatement;

class QueryBuilder
{

    /**
     * instance of PDO
     * @var PDO $connection 
     */
    protected $connection;

    /**
     * table of Query
     * @var string $table
     */
    protected $table;

    /**
     * @var array $columns
     */
    protected $columns = [];

    /**
     * @var array $conditions
     */
    protected $conditions = [];

    /**
     * @var array $orderBy
     */
    protected $orderBy = [];

    public function __construct(Connection $connection, $table)
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    /**
     * @param array $columns
     * @return QueryBuilder
     */
    public function select($columns = [])
    {
        if (!empty($columns)) {
            $this->columns = $columns;
        }
        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function where($column, $operator, $value)
    {
        $this->conditions[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * @param string $direction
     * 
     * @return QueryBuilder
     */
    public function orderBy($column, $direction = 'ASC')
    {
        $this->orderBy[] = [
            'column' => $column,
            'direction' => $direction
        ];
        return $this;
    }

    public function get()
    {
        $query = $this->buildQuery();
        $params = $this->buildParams();

        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            throw $exception;
        }
    }

    /**
     * @return string
     */
    protected function buildQuery(): string
    {
        $query = "SELECT ";

        if (!empty($this->columns)) {
            $query .= implode(", ", $this->columns);
        } else {
            $query .= "*";
        }

        $query .= " FROM " . $this->table;

        if (!empty($this->conditions)) {
            $query .= " WHERE ";
            $conditions = [];
            foreach ($this->conditions as $condition) {
                $conditions[] = "{$condition['column']} {$condition['operator']} ?";
            }
            $query .= implode(" AND ", $conditions);
        }

        if (!empty($this->orderBy)) {
            $query .= " ORDER BY ";
            $orderBy = [];
            foreach ($this->orderBy as $order) {
                $orderBy[] = "{$order['column']} {$order['direction']}";
            }
            $query .= implode(", ", $orderBy);
        }

        return $query;
    }

    protected function buildParams()
    {
        $params = [];
        foreach ($this->conditions as $condition) {
            $params[] = $condition['value'];
        }
        return $params;
    }
}

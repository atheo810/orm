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
     * @var array $select
     */
    protected $select = [];

    /**
     * @var string $from
     */
    protected $from = '';

    /**
     * @var array $join
     */
    protected $join = [];

    /**
     * @var array $where
     */
    protected $where = [];

    /**
     * @var string $orderBy
     */
    protected $orderBy = '';

    /**
     * @var string $limit
     */
    protected $limit = '';

    /**
     * @var array $params
     */
    protected $params = [];

    /**
     * QueryBuilder constructor.
     *
     * @param string $table
     * @param PDO $connection
     */
    public function __construct(string $table, PDO $connection)
    {
        $this->table = $table;
        $this->connection = $connection;
    }

    /**
     * Set the columns to be selected.
     *
     * @param array $columns
     * @return QueryBuilder
     */
    public function select(array $columns = ['*']):QueryBuilder
    {
        $this->select = $columns;
        return $this;
    }

    /**
     * Set the table to select from.
     *
     * @param string $table
     * @return QueryBuilder
     */
    public function from(string $table):QueryBuilder
    {
        $this->from = $table;
        return $this;
    }

    /**
     * Add a join clause to the query.
     *
     * @param string $table
     * @param string $column1
     * @param string $operator
     * @param string $column2
     * @return QueryBuilder
     */
    public function join(string $table, string $column1, string $operator, string $column2):QueryBuilder
    {
        $this->join[] = "JOIN {$table} ON {$column1} {$operator} {$column2}";
        return $this;
    }

    /**
     * Add a where clause to the query.
     *
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @return QueryBuilder
     */
    public function where(string $column, string $operator, $value):QueryBuilder
    {
        $this->where[] = "{$column} {$operator} ?";
        $this->params[] = $value;
        return $this;
    }

    /**
     * Add an order by clause to the query.
     *
     * @param string $column
     * @param string $direction
     * @return QueryBuilder
     */
    public function orderBy(string $column, string $direction = 'ASC'):QueryBuilder
    {
        $this->orderBy = "{$column} {$direction}";
        return $this;
    }

    /**
     * Add a limit clause to the query.
     *
     * @param int $limit
     * @return QueryBuilder
     */
    public function limit(int $limit):QueryBuilder
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Execute the query and return the result.
     *
     * @return array
     * @throws PDOException
     */
    public function get(): array
    {
        $columns = implode(', ', $this->select);
        $query = "SELECT {$columns} FROM {$this->table} {$this->from}";

        if (!empty($this->join)) {
            $query .= ' ' . implode(' ', $this->join);
        }

        if (!empty($this->where)) {
            $query .= ' WHERE ' . implode(' AND ', $this->where);
        }

        if (!empty($this->orderBy)) {
            $query .= ' ORDER BY ' . $this->orderBy;
        }

        if (!empty($this->limit)) {
            $query .= ' LIMIT ' . $this->limit;
        }

        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($this->params);
            return $statement->fetchAll();
        } catch (PDOException $exception) {
            throw $exception;
        }
    }
}

<?php

namespace Atheo\Orm;

use PDO;
use PDOStatement;
use Serializable;

class Connection implements Serializable
{

    /**
     * @var string $username
     */
    protected $username;

    /**
     * @var string $password
     */
    protected $password;

    /**
     * @var bool $logQueries
     */
    protected $logQueries = false;

    /**
     * @var array $log
     */
    protected $log = [];

    /**
     * @var array $commands
     */
    protected $commands;

    /**
     * PDO Connection Options
     * @var array $options
     */
    protected $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    /**
     * PDO object
     * @var PDO 
     */
    protected $pdo;

    /**
     * @var SQL\Compiler
     */
    protected $compiler;

    /** @var    Schema\Compiler The schema compiler associated with this connection */
    protected $schemaCompiler;

    /**
     * The DSN for this connection 
     *  @var string  $dsn
     * */
    protected $dsn;

    /**
     * @var string $driver
     */
    protected $driver;

    /**
     * @var Schema $schema
     */
    protected $schema;

    /**
     * @var array $compilerOptions
     */
    protected $compilerOptions = [];

    /**
     * @var array $schemaCompilerOptions
     */
    protected $schemaCompilerOptions = [];

    /**
     * @var bool $throwTransactionExceptions
     */
    protected $throwTransactionExceptions = false;

    /**
     * Constructor
     *
     * @param   string $dsn The DSN string
     * @param   string $username (optional) Username
     * @param   string $password (optional) Password
     * @param   string $driver (optional) Driver's name
     * @param   PDO $pdo (optional) PDO object
     */
    public function __construct(
        string $dsn = null,
        string $username = null,
        string $password = null,
        string $driver = null,
        PDO $pdo = null
    ) {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->driver = $driver;
        $this->pdo = $pdo;
    }

    /**
     * @param PDO $pdo
     * @return Connection
     */
    public static function fromPDO(PDO $pdo): self
    {
        return new static(null, null, null, null, $pdo);
    }

    /**
     * Enable or disable query logging
     *
     * @param bool $value (optional) Value
     *
     * @return Connection
     */
    public function logQueries(bool $value = true): self
    {
        $this->logQueries = $value;
        return $this;
    }

    /**
     * @param bool $value
     * @return Connection
     */
    public function throwTransactionExceptions(bool $value = true): self
    {
        $this->throwTransactionExceptions = $value;
        return $this;
    }

    /**
     * Add an init command
     *
     * @param string $query SQL command
     * @param array $params (optional) Params
     *
     * @return  Connection
     */
    public function initCommand(string $query, array $params = []): self
    {
        $this->commands[] = [
            'sql' => $query,
            'params' => $params,
        ];

        return $this;
    }

    /**
     * Set the username
     *
     * @param string $username Username
     *
     * @return Connection
     */
    public function username(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Set the password
     *
     * @param   string $password Password
     *
     * @return  Connection
     */
    public function password(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Set PDO connection options
     *
     * @param   array $options PDO options
     *
     * @return  Connection
     */
    public function options(array $options): self
    {
        foreach ($options as $name => $value) {
            $this->option($name, $value);
        }

        return $this;
    }

    /**
     * Set a PDO connection option
     *
     * @param  mixed $name
     * @param  mixed $value
     *
     * @return  Connection
     */
    public function option($name, $value): self
    {
        $this->options[$name] = $value;
        return $this;
    }

    /**
     * Use persistent connections
     *
     * @param bool $value (optional) Value
     *
     * @return Connection
     */
    public function persistent(bool $value = true): self
    {
        return $this->option(PDO::ATTR_PERSISTENT, $value);
    }

    /**
     * Set date format
     *
     * @param string $format Date format
     *
     * @return Connection
     */
    public function setDateFormat(string $format): self
    {
        $this->compilerOptions['dateFormat'] = $format;
        return $this;
    }

    /**
     * Set identifier wrapper
     *
     * @param   string $wrapper Identifier wrapper
     *
     * @return  Connection
     */
    public function setWrapperFormat(string $wrapper): self
    {
        $this->compilerOptions['wrapper'] = $wrapper;
        $this->schemaCompilerOptions['wrapper'] = $wrapper;
        return $this;
    }
}

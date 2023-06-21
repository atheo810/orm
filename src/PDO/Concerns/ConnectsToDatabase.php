<?php

namespace Atheo\Orm\PDO\Concerns;

use Atheo\Orm\PDO\Connection;
use InvalidArgumentException;
use PDO;

trait ConnectsToDatabase
{
    /**
     * Create a new database connection.
     *
     * @param  mixed[]  $params
     * @param  string|null  $username
     * @param  string|null  $password
     * @param  mixed[]  $driverOptions
     * @return \Atheo\Orm\PDO\Connection
     *
     * @throws InvalidArgumentException
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = [])
    {
        if (!isset($params['pdo']) || !$params['pdo'] instanceof PDO) {
            throw new InvalidArgumentException('Indoframe requires the "pdo" property to be set and be a PDO instance.');
        }

        return new Connection($params['pdo']);
    }
}

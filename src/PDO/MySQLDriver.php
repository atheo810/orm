<?php

namespace Atheo\Orm\PDO;
use Atheo\Orm\PDO\Concerns\ConnectsToDatabase;

class MySQLDriver
{
    use ConnectsToDatabase;

    public function getName(){
        return 'pdo_mysql';
    }
}

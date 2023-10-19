<?php

namespace Spaal\Infrastructure\Perscistence;

use PDO;

class ConnectionCreator
{
    public static function createConnection(): PDO
    {
        #'oci:192.100.100.245/GLPROD', 'glprod', 'dbaspl'
        $db_username = 'glprod';
        $db_password = 'dbaspl';
        $db = 'oci:dbname=192.100.100.245/GLPROD;charset=AL32UTF8';
        $connection = new PDO($db,$db_username,$db_password);
        return $connection;
    }
}
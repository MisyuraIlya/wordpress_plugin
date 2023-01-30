<?php

namespace App;

require_once __DIR__ . '/../../vendor/autoload.php';

use PDO;
use PDOException;

class ConnectionDb
{
    public function __construct()
    {
        $servername = "localhost";
        $username = "pluginwp";
        $password = "yA8oR6vK9b";

        try {
            $this->conn = new PDO("mysql:host=$servername;dbname=pluginwp", $username, $password,[
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
            ]);
            // set the PDO error mode to exception

//            echo "Connected successfully";
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

    }

    public function dbQuery($query)
    {
        $data = $this->conn->query($query);
        $res = $data->fetch();

        return $res;
    }
}
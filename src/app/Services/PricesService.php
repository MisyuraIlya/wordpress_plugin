<?php

namespace App\Services;

require_once __DIR__ . '/../../../vendor/autoload.php';

use App\ConnectionDb;
use App\DAL\PricesQuery;

class PricesService
{
    function __construct($data, $skip)
    {

        $this->skip = $skip;
        $this->data = $data;
        $this->db = new ConnectionDb();
    }

    private function fetchLiveQuries($projectName)
    {
        $query = "SELECT * FROM live_queries WHERE project_name = '$projectName'";
        $response = $this->db->dbQuery($query);
        return $response;
    }

    private function fetchStaticQuries($projectName)
    {
        $query = "SELECT * FROM static_queries WHERE project_name = '$projectName'";
        $response = $this->db->dbQuery($query);
        return $response;
    }

    private function fetchQuery($projectName)
    {
        $query = "SELECT * FROM query WHERE project_name = '$projectName'";
        $response = $this->db->dbQuery($query);
        return $response;
    }


    public function logic()
    {
        $queries = $this->fetchQuery($this->data->project);
        $response = (new PricesQuery($queries->get_prices,$queries->project_name,$this->data, $this->skip))->response();
        return $response;
    }
}
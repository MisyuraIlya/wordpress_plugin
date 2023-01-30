<?php

namespace App\Services;
require_once __DIR__ . '/../../../vendor/autoload.php';
use App\ConnectionDb;
use App\DAL\AttributesQuery;

class AttributesService
{
    function __construct($data, $page)
    {

        $this->page = $page;
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
        $response = (new AttributesQuery($queries->get_stocks,$queries->project_name,$this->data, $this->page))->response();
        return $response;
    }
}
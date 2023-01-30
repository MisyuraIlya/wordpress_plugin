<?php

namespace App\Services;
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\ConnectionDb;
use App\DAL\FilterByQuery;
class FilterByService
{
    function __construct($data, $skip,$function,$sku)
    {

        $this->skip = $skip;
        $this->data = $data;
        $this->db = new ConnectionDb();
        $this->function = $function;
        $this->sku = $sku;
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
        $response = (new FilterByQuery($queries,$queries->project_name,$this->data, $this->skip,$this->function,$this->sku))->response();
        return $response;
    }
}
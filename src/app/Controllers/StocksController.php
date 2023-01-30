<?php

namespace App\Controllers;

require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Services\StocksService;
use App\Services\FilterByService;
class StocksController
{
    function __construct(\stdClass $data, int $page,$sku)
    {
        $this->page = $page;
        $this->data = $data;
        $this->sku = $sku;
    }

    private function serviceCheck()
    {
        if(!empty($this->sku)){
            return true;
        } else {
            return false;
        }
    }


    public function result()
    {
        if($this->serviceCheck()){
            $result = (new FilterByService($this->data,$this->page,'FilterByStock',$this->sku ))->logic();
        } else {
            $result = (new StocksService($this->data,$this->page))->logic();

        }

        return $result;

    }
}
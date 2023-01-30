<?php

namespace App\Controllers;

require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Services\PricesService;
use App\Services\FilterByService;
class PricesController
{

    function __construct(\stdClass $data, int $skip,$sku)
    {
        $this->skip = $skip;
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
            $result = (new FilterByService($this->data,$this->skip,'FilterByPrice',$this->sku ))->logic();
        } else {
            $result = (new PricesService($this->data,$this->skip))->logic();

        }

        return $result;

    }
}
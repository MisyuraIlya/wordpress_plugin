<?php

namespace App\Controllers;

require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Services\ProductsService;
use App\Services\FilterByService;

class ProductsController
{

    function __construct(\stdClass $data, int $skip, $sku)
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
            $result = (new FilterByService($this->data,$this->skip,'FilterByProduct',$this->sku ))->logic();
        } else {
            $result = (new ProductsService($this->data,$this->skip))->logic();

        }

        return $result;

    }
}
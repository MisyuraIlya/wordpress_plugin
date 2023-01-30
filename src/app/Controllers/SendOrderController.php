<?php

namespace App\Controllers;

use App\Services\SendOrderService;

class SendOrderController
{
    function __construct(\stdClass $data,$json)
    {
        $this->data = $data;
        $this->json = $json;
    }

    public function result()
    {
        $result = (new SendOrderService($this->data,$this->json))->logic();
        return $result;

    }
}
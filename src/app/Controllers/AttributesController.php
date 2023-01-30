<?php

namespace App\Controllers;
require_once __DIR__ . '/../../../vendor/autoload.php';
use App\Services\AttributesService;
class AttributesController
{
    function __construct(\stdClass $data,  $page)
    {

        $this->page = $page;
        $this->data = $data;
    }

    public function result()
    {
        $result = (new AttributesService($this->data,$this->page))->logic();

        return $result;

    }
}
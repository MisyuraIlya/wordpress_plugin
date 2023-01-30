<?php

namespace App\Controllers;

require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Services\CategoriesService;

class CategoriesController
{
    function __construct(\stdClass $data,$page)
    {
        $this->data = $data;
        $this->page = $page;
    }

    public function result()
    {
        $result = (new CategoriesService($this->data,$this->page))->logic();

        return $result;

    }
}
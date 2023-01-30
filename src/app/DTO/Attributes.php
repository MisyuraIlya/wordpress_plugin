<?php

namespace App\DTO;

class Attributes
{
    public function __construct(
         $id_erp,
         $name,
         $options
    )
    {
        $this->id_erp = $id_erp;
        $this->name = $name;
        $this->options = $options;
    }

    public function createObj()
    {
        $result = new \stdClass();
        $result->id_erp = $this->id_erp;
        $result->name = $this->name;
        $result->options = $this->options;
        return $result;
    }
}
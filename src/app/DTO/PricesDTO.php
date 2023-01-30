<?php

namespace App\DTO;

class PricesDTO
{
    public function __construct(
        $sku,
        $price
    )
    {
        $this->sku = $sku;
        $this->price = $price;

    }

    public function createObj()
    {
        $result = new \stdClass();
        $result->sku = $this->sku;
        $result->price = $this->price;

        return $result;
    }
}
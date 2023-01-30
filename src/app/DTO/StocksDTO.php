<?php

namespace App\DTO;

class StocksDTO
{
    public function __construct(
        $sku,
        $stock_quantity
    )
    {
        $this->sku = $sku;
        $this->stock_quantity = $stock_quantity;
    }

    public function createObj()
    {
        $result = new \stdClass();
        $result->sku = $this->sku;
        $result->stock_quantity = $this->stock_quantity;
        return $result;
    }
}
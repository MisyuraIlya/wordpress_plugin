<?php

namespace App\DTO;

class CustomFields
{
    public function __construct(
        $barcode,
        $vatLiable,
        $salesItem,
        $expiry_date,
        $unit_of_weight,
        $weight_for_bulk_food,
        $capacity,
        $new_arrivals,
        $new_seller,
        $packing_characteristics
    )
    {
        $this->barcode = $barcode;
        $this->vatLiable = $vatLiable;
        $this->salesItem = $salesItem;
        $this->expiry_date = $expiry_date;
        $this->unit_of_weight = $unit_of_weight;
        $this->weight_for_bulk_food = $weight_for_bulk_food;
        $this->capacity = $capacity;
        $this->new_arrivals =  $new_arrivals;
        $this->new_seller = $new_seller;
        $this->packing_characteristics = $packing_characteristics;

    }

    public function createObj()
    {
        $result = new \stdClass();
        $result->barcode = $this->barcode;
        $result->vatLiable = $this->vatLiable;
        $result->salesItem = $this->_alesItem ;
        $result->expiry_date = $this->expiry_date ;
        $result->unit_of_weight = $this->unit_of_weight;
        $result->weight_for_bulk_food = $this->weight_for_bulk_food;
        $result->capacity = $this->capacity;
        $result->new_arrivals = $this->new_arrivals;
        $result->new_seller = $this->new_seller;
        $result->packing_characteristics = $this->packing_characteristics;

    }

}

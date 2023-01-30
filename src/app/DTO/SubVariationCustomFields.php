<?php

namespace App\DTO;

class SubVariationCustomFields
{
    public function __construct(
        string $barcode_variation,
        array $image_certificate_gallery,
        string $promotion,
        array $product_specification,
        array $table_with_tabs,
        string $expiry_date,
        string $unit_of_weight,
        int $weight_for_bulk_food,
        int $capacity,
        string $new_arrivals,
        string $new_seller,
        string $packing_characteristics,
        array $image_gallery
    )
    {
        $this->barcode_variation = $barcode_variation;
        $this->image_certificate_gallery = $image_certificate_gallery;
        $this->promotion = $promotion;
        $this->product_specification = $product_specification;
        $this->table_with_tabs = $table_with_tabs;
        $this->expiry_date = $expiry_date;
        $this->unit_of_weight = $unit_of_weight;
        $this->weight_for_bulk_food = $weight_for_bulk_food;
        $this->capacity = $capacity;
        $this->new_arrivals = $new_arrivals;
        $this->new_seller = $new_seller;
        $this->packing_characteristics = $packing_characteristics;
        $this->image_gallery = $image_gallery;









    }
}
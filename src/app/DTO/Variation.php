<?php

namespace App\DTO;

use App\DTO\SubVariationCustomFields;

class Variation
{

    public function __construct(
        string $image,
        int $stock_quantity,
        string $sku,
        int $regular_price,
        int $discount,
        string $description,
        array $arrtibutes,
        int $weight,
        int $length,
        int $width,
        int $height,
        public SubVariationCustomFields $custom_fields,
        int $weight_for_bulk_food,
        string $new_arrivals,
        string $new_seller,
    )
    {

    }
}
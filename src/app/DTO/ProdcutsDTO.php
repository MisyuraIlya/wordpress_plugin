<?php

namespace App\DTO;

use App\DTO\CategoryIds;
use App\DTO\Attributes;
use App\DTO\SubVariationCustomFields;
use App\DTO\Variation;
use App\DTO\CustomFields;
use App\Enums\GeneralSettings;

class ProdcutsDTO
{
    public function __construct(
         $sku,
         $name,
         $titleEng,
         $category_ids,
         $description,
         $image,
         $visibility,
         $weight,
         $length,
         $width,
         $height,
         $image_gallery,
         CustomFields $custom_fields,
         $variation,
         $attributes,
        $default_variation
    )
    {
        $this->sku = $sku;
        $this->name = $name;
        $this->titleEng = $titleEng;
        $this->category_ids = $category_ids;
        $this->description = $description;
        $this->image = $image;
        $this->visibility = $visibility;
        $this->weight =$weight;
        $this->length = $length;
        $this->width = $width;
        $this->height = $height;
        $this->image_gallery = $image_gallery;
        $this->custom_fields = $custom_fields;
        $this->variation = $variation;
        $this->attributes = $attributes;
        $this->default_variation = $default_variation;
    }

    public function createObj()
    {
        $result = new \stdClass();
        $result->sku = $this->sku;
        $result->name = $this->name;
        $result->titleEng = $this->titleEng;
        $result->category_ids = $this->category_ids;
        $result->description = $this->description;
        $result->image = $this->image;
        $result->visibility = $this->visibility;
        $result->vat = GeneralSettings::VAT;

        $result->weight = $this->weight;
        $result->length = $this->length;
        $result->width = $this->width;
        $result->height = $this->height;
        $result->image_gallery = $this->image_gallery;
        $result->custom_fields = $this->custom_fields;
        $result->variation = $this->variation;
        $result->attributes = $this->attributes;
        $result->default_variation = $this->default_variation;
        return $result;
    }

}

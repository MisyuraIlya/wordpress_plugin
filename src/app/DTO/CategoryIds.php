<?php

namespace App\DTO;

class CategoryIds
{
    public function __construct(int $numberOfCategory)
    {
        $this->category_ids = $numberOfCategory;
    }
}
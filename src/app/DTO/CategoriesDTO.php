<?php

namespace App\DTO;

class CategoriesDTO
{
 public function __construct(
     $exId,
     $name,
     $description,
     $image,
     $lvlNumber,
     $parent,
     $visibility
 )
 {
     $this->exId = $exId;
     $this->name = $name;
     $this->description = $description;
     $this->image = $image;
     $this->lvlNumber = $lvlNumber;
     $this->parent = $parent;
     $this->visibility = $visibility;



 }

 public function createObj()
 {
     $result = new \stdClass();
     $result->exId = $this->exId;
     $result->name = $this->name;
     $result->description = $this->description;
     $result->image = $this->image;
     $result->lvlNumber = $this->lvlNumber;
     $result->parent = $this->parent;
     $result->visibility = $this->visibility;


     return $result;
 }
}
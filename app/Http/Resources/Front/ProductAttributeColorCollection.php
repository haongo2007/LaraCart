<?php

namespace App\Http\Resources\Front;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductAttributeColorCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $res = [];
        foreach ($this->resource as $key => $value) {
            $res[] = [
                'id' => $value->id,
                'color' => new ProductAttributePaletteCollection($value->first_palette),
                'color_name' => $value->name,
                'price' => $value->add_price,
                'size' => new ProductAttributeSizeCollection($value->size),
            ];
        }
        
        return $res;
    }
}


class ProductAttributeSizeCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $res = [];
        foreach ($this->resource as $key => $value) {
            $res[] = [
                'id' => $value->id,
                'name' => $value->name,
                'price' => $value->add_price,
            ];
        }
        
        return $res;
    }
}

class ProductAttributePaletteCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {        
        return $this->hex;
    }
}
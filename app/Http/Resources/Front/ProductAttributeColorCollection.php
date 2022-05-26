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
            
            $group = strtolower($value->attGroup->name);

            if (!isset($res[$group])) {
                $res[$group] = [];
            }
            
            array_push($res[$group], 
            [
                'id' => $value->id,
                'color' => new ProductAttributePaletteCollection($value->activePalette),
                'name' => $value->name,
                'price' => $value->add_price,
                'children' => new ProductAttributeSizeCollection($value->Children),
            ]);
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
            $group = strtolower($value->attGroup->name);
            
            if (!isset($res[$group])) {
                $res[$group] = [];
            }
            
            array_push($res[$group], 
            [
                'id' => $value->id,
                'name' => $value->name,
                'price' => $value->add_price,
                'color' => new ProductAttributePaletteCollection($value->first_palette),
            ]);
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
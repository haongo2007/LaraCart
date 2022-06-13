<?php

namespace App\Http\Resources\Front;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Front\ProductAttributeColorCollection;

class ProductCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $res = [
            'id'=> $this->id ,
            'author'=> $this->creator,
            'brands'=> $this->brand,
            'category'=> $this->categoriesWithLangDefault,
            'featured'=> null,
            'name'=> $this->name,
            'new'=> null,
            'pictures'=> explode(',',$this->image) ,
            'price'=> $this->price,
            'ratings'=> 5,
            'review'=> 2,
            'sale_price'=> $this->promotionPrice,
            'short_desc'=> $this->getText(),
            'slug'=> $this->alias,
            'sold'=> $this->sold,
            'stock'=> $this->stock ,
            'top'=> null,
            'until' => null,
            'tax' => $this->getTaxValue(),
            'variants' => new ProductAttributeColorCollection($this->attributesParent)
        ];
        
        return $res;
    }
}

class ProductRelatedCollection extends JsonResource
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
                'id'=> $value->id ,
                'author'=> $value->creator,
                'brands'=> $value->brand,
                'category'=> $value->categoriesWithLangDefault,
                'featured'=> null,
                'name'=> $value->name,
                'new'=> null,
                'pictures'=> explode(',',$value->image) ,
                'price'=> $value->price,
                'ratings'=> 5,
                'review'=> 2,
                'sale_price'=> $value->promotionPrice,
                'short_desc'=> $value->getText(),
                'slug'=> $value->alias,
                'sm_pictures'=> $value->image.'&w=575',
                'sold'=> $value->sold,
                'stock'=> $value->stock ,
                'top'=> null,
                'until' => null,
                'variants' => new ProductAttributeColorCollection($value->attributes)
            ];
        }
        return $res;
    }
}
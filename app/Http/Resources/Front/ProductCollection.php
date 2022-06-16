<?php

namespace App\Http\Resources\Front;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Front\ProductAttributeColorCollection;
use App\Http\Resources\Front\ProductRatingCollection;

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
            'id'=> $this->id,
            'author'=> $this->creator,
            'brands'=> $this->brand,
            'category'=> $this->categoriesWithLangDefault,
            'name'=> $this->name,
            'pictures'=> explode(',',$this->image) ,
            'price'=> $this->price,
            'ratings'=> $this->rate_count == 0 ? 0 : $this->rate_point / $this->rate_count,
            'review'=> $this->rate_count,
            'reviewList'=> new ProductRatingCollection($this->ratingList),
            'sale_price'=> $this->promotionPrice,
            'short_desc'=> $this->getText(),
            'slug'=> $this->alias,
            'sold'=> $this->sold,
            'stock'=> $this->stock ,
            'featured'=> null,
            'new'=> null,
            'until' => null,
            'top' => $this->top,
            'rate_point' => $this->rate_point,
            'rate_count' => $this->rate_count,
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
                'name'=> $value->name,
                'pictures'=> explode(',',$value->image) ,
                'price'=> $value->price,
                'ratings'=> $value->rate_count == 0 ? 0 : $value->rate_point / $value->rate_count,
                'review'=> $value->rate_count,
                'sale_price'=> $value->promotionPrice,
                'short_desc'=> $value->getText(),
                'slug'=> $value->alias,
                'sm_pictures'=> $value->image.'&w=575',
                'sold'=> $value->sold,
                'stock'=> $value->stock ,
                'top'=> $value->top,
                'tax' => $value->getTaxValue(),
                'featured'=> null,
                'new'=> null,
                'until' => null,
                'variants' => new ProductAttributeColorCollection($value->attributes)
            ];
        }
        return $res;
    }
}
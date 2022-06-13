<?php

namespace App\Http\Resources\Front;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Front\ProductCollection;

class ProductFlashSaleCollection extends JsonResource
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
            'product' => new ProductCollection($this->product),
        ];
        
        return $res;
    }
}
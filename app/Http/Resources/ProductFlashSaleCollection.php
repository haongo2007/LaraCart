<?php

namespace App\Http\Resources;

use App\Http\Resources\ProductCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductForSaleCollection extends JsonResource
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
            'id' => $this->id ,
            'image' => $this->image ,
            'description' => $this->getText() ,
            'store' => $this->store,
        ];
        
        return $res;
    }
}

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
        return [
            'id'            => $this->id,
            'product'       => new ProductForSaleCollection($this->product),
            'stock'         => $this->stock,
            'sold'          => $this->sold,
            'promotion'     => $this->promotion,
            'sort'          => $this->sort,
        ];
    }
}

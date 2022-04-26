<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\StoreCollection;

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
            'id' => $this->id ,
            'alias' => $this->alias ,
            'content' => $this->content,
            'cost' => $this->cost ,
            'created_at' => $this->created_at ,
            'date_available' => $this->date_available ,
            'date_lastview' => $this->date_lastview ,
            'categories' => $this->categoriesWithLangDefault ,
            'height' => $this->height ,
            'image' => $this->image ,
            'keyword' => $this->keyword ,
            'kind' => $this->kind ,
            'lang' => $this->lang ,
            'length' => $this->length ,
            'length_class' => $this->length_class ,
            'minimum' => $this->minimum ,
            'name' => $this->name ,
            'price' => $this->price ,
            'property' => $this->property ,
            'sku' => $this->sku ,
            'sold' => $this->sold ,
            'sort' => $this->sort ,
            'status' => $this->status ,
            'stock' => $this->stock ,
            'store' => $this->store,
            'supplier_id' => $this->supplier_id ,
            'attributes' => $this->attributes ,
            'tax' => $this->getTaxValue() ,
            'updated_at' => $this->updated_at ,
            'view' => $this->view ,
            'weight' => $this->weight ,
            'weight_class' => $this->weight_class ,
            'width' => $this->width ,
        ];
        
        return $res;
    }
}

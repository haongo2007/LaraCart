<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportProductCollection extends JsonResource
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
            'cost' => $this->cost ,
            'image' => $this->image ,
            'price' => $this->price ,
            'date_lastview' => $this->date_lastview ,
            'kind' => $this->kind ,
            'name' => $this->name ,
            'sku' => $this->sku ,
            'sold' => $this->sold ,
            'status' => $this->status ,
            'stock' => $this->stock ,
            'store' => $this->store,
            'view' => $this->view ,
        ];
        
        return $res;
    }
}

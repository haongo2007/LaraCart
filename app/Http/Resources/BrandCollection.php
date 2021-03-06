<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandCollection extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'alias' => $this->alias,
            'image' => $this->image,
            'status' => $this->status,
            'sort' => $this->sort,
            'store' => $this->stores,
        ];
        return $res;
    }
}

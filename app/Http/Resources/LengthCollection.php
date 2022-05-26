<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LengthCollection extends JsonResource
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
            'description' => $this->description,
            'store' => $this->stores,
        ];
        return $res;
    }
}

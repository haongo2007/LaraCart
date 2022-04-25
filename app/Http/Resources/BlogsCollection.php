<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogsCollection extends JsonResource
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
            'image' => $this->image,
            'alias' => $this->alias,
            'sort' => $this->sort,
            'status' => $this->status,
            'store' => $this->store,
        ];
        return $res;
    }
}

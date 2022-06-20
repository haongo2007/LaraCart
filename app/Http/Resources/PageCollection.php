<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PageCollection extends JsonResource
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
            'alias' => $this->alias,
            'image' => $this->image,
            'status' => $this->status,
            'store' => $this->store,
            'title' => $this->title,
            'lang' => $this->lang,
        ];
        return $res;
    }
}

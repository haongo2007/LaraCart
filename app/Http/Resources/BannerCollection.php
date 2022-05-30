<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BannerCollection extends JsonResource
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
            'title' => $this->title,
            'image' => $this->image,
            'url' => $this->url,
            'target' => $this->target,
            'click' => $this->click,
            'type' => $this->type,
            'status' => $this->status,
            'store' => $this->store,
            'html' => $this->html,
            'sort' => $this->sort,
        ];
        return $res;
    }
}

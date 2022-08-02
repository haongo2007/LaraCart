<?php

namespace App\Http\Resources\Front;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

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
        return  [
                    'id' => $this->id,
                    'image' => $this->image,
                    'alias' => $this->alias,
                    'created_at' => Carbon::parse($this->created_at)->format('M d,Y'),
                    'descriptions' => $this->descriptionsWithLangDefault,
                ];
    }
}

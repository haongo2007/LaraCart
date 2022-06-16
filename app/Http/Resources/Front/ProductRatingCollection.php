<?php

namespace App\Http\Resources\Front;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ProductRatingCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $res = [];
        foreach ($this->resource as $key => $value) {
            $res[] = [
                'id' => $value->id,
                'comment' => $value->comment,
                'point' => $value->point,
                'name' => $value->Customer->first_name.' '.$value->Customer->last_name,
                'created_at' => Carbon::parse($value->created_at)->diffForHumans(),
            ];
        }
        return $res;
    }
}

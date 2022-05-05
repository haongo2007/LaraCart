<?php

namespace App\Http\Resources\Front;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryCollection extends JsonResource
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
                'parent' => $value->parent,
                'name' => $value->descriptionsWithLangDefault->title,
                'sort' => $value->sort,
                'top' => $value->top,
                'alias' => $value->alias,
                'image' => $value->image,
                'hasChild' => !$value->Parent ? true : false
            ];
        }
        return $res;
    }
}

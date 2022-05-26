<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomFieldCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'   => $this->id,
            'type' => $this->type,
            'code' => $this->code,
            'name' => $this->name,
            'required' => $this->required,
            'status' => $this->status,
            'option' => $this->option,
            'default' => $this->default,
            'store' => $this->stores
        ];
    }
}

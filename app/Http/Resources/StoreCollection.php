<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreCollection extends JsonResource
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
            'id' => $this->id,
            'logo' => $this->logo,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'template' => $this->template,
            'domain' => $this->domain,
            'currency' => $this->currency,
            'status' => $this->status,
            'active' => $this->active
        ];
    }
}

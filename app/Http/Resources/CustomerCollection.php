<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerCollection extends JsonResource
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
            'value'      => $this->first_name.' '.$this->last_name.($this->email ? ' <'.$this->email.'>' : ''),
            'id'         => $this->id,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'address1'   => $this->address1,
            'address2'   => $this->address2,
            'address3'   => $this->address3,
            'phone'      => $this->phone,
            'email'      => $this->email,
            'country'    => $this->country
        ];
    }
}

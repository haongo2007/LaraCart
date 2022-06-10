<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\StoreCollection;

class OrderCollection extends JsonResource
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
            'id'            => $this->id,
            'first_name'    => $this->first_name,
            'last_name'     => $this->last_name,
            'address1'      => $this->address1,
            'address2'      => $this->address2,
            'address3'      => $this->address3,
            'phone'         => $this->phone,
            'email'         => $this->email,
            'subtotal'      => $this->subtotal,
            'currency'      => $this->currency,
            'shipping'      => $this->shipping,
            'discount'      => $this->discount,
            'country'       => $this->country,
            'total'         => $this->total,
            'payment_method'=> $this->payment_method,
            'exchange_rate' => $this->exchange_rate,
            'status'        => $this->status,
            'created_at'    => $this->created_at,
            'tax'        => $this->tax,
            'store'        => $this->stores
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DiscountCollection extends JsonResource
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
            'id'        => $this->id,
            'code'      => $this->code,
            'reward'    => $this->reward,
            'type'      => $this->type,
            'point'     => $this->point,
            'data'      => $this->data,
            'limit'     => $this->limit,
            'used'      => $this->used,
            'login'     => $this->login,
            'store'     => $this->store,
            'expires_at'=> $this->expires_at,
            'status'    => $this->status
        ];
    }
}

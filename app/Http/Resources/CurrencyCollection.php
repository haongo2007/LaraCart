<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyCollection extends JsonResource
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
            'id'         => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'symbol' => $this->symbol,
            'exchange_rate' => $this->exchange_rate,
            'precision' => $this->precision,
            'symbol_first' => $this->symbol_first,
            'thousands' => $this->thousands,
            'status' => $this->status,
            'sort' => $this->sort,
            'store' => $this->stores
        ];
    }
}

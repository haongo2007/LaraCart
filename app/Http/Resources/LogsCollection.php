<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LogsCollection extends JsonResource
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
            'user'       => $this->user,
            'path'          => $this->path,
            'method'        => $this->method,
            'ip'            => $this->ip,
            'user_agent'    => $this->user_agent,
            'input'         => $this->input,
            'created_at'    => $this->created_at,
        ];
    }
}

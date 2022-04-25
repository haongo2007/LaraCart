<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\StoreCollection;


class UserCollection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {   
        return [
            'id' => $this->id,
            'fullname' => $this->fullname,
            'email' => $this->email,
            'phone' => $this->phone,
            'roles' => $this->roles,
            'permissions' => $this->permissions,
            'avatar' => $this->avatar ? asset($this->avatar) : 'api/getFile?disk='.env('FILESYSTEM_DRIVER', 'local').'&path='.urlencode('avatar/default.jpg'),
            'store' => StoreCollection::collection($this->stores),
        ];
    }
}

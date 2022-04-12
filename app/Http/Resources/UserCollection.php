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
            'name' => $this->name,
            'email' => $this->email,
            'roles' => array_map(
                function ($role) {
                    return $role['name'];
                },
                $this->roles->toArray()
            ),
            'avatar' => $this->avatar ? asset($this->avatar) : 'api/getFile?disk='.env('FILESYSTEM_DRIVER', 'local').'&path='.urlencode('avatar/default.jpg'),
            'store' => StoreCollection::collection($this->stores)
        ];
    }
}

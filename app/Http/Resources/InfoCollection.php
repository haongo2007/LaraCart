<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Admin\Config;

class InfoCollection extends JsonResource
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
            'name' => $this->fullname,
            'email' => $this->email,
            'avatar' => $this->avatar ? asset($this->avatar) : 'api/getFile?disk='.env('FILESYSTEM_DRIVER', 'local').'&path='.urlencode('avatar/default.jpg'),
            'roles' => array_map(
                function ($role) {
                    return $role['slug'];
                },
                $this->roles->toArray()
            ),
            'permissions' => array_map(
                function ($permission) {
                    return $permission['slug'];
                },
                $this->allPermissions()->toArray()
            ),
            'store' => $this->listStore(),
        ];
    }
}

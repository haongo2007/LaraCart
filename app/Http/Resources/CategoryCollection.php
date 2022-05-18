<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $res = [
            'id' => $this->id,
            'name' => $this->descriptionsWithLangDefault->title,
            'status' => $this->status,
            'sort' => $this->sort,
            'top' => $this->top,
            'alias' => $this->alias,
            'parent_id' => $this->parent,
            'parent' => $this->parent,
            'store' => $this->stores,
            'image' => $this->image
        ];

        if (!$parent = $request->parent === '0' ? true : false) {
            if ($this->Parent) {
                $res['parent'] = $this->Parent->descriptionsWithLangDefault->title;
                if (($request->id || $request->parent_list) && !$this->Children->where('store_id',$this->stores->id)->isEmpty()) {
                    $res['hasChildren'] = true;
                }
            }else{
                $res['hasChildren'] = true;
            }
        }else{
            if (!$this->Children->where('store_id',$this->stores->id)->isEmpty()) {
                $res['hasChildren'] = true;
            }
        } 
        
        return $res;
    }
}

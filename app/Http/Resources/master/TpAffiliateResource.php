<?php

namespace App\Http\Resources\master;

use Illuminate\Http\Resources\Json\JsonResource;

class TpAffiliateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => $this->status, //($this->status == "S" ? true : false),
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}

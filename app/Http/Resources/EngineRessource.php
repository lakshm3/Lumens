<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EngineRessource extends JsonResource
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
            "human_id" =>$this->id,
            "id"=>$this->id
        ];
        return parent::toArray($request);
    }
}

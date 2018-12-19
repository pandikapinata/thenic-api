<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Activity extends JsonResource
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
            'activityId' => $this->id,
            'activityName' => $this->name,
            'activityIcon' => "http://192.168.43.74:8000/images/".$this->task_icon,
        ];
    }
}

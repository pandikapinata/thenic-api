<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskSync extends JsonResource
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
            'taskId' => $this->id,
            'activityId' => $this->activity_id,
            'taskName' => $this->activity->name,
            'taskNote' => $this->note,
            'taskSets' => $this->sets,
            'taskReps' => $this->repetition,
            'taskVolume' => $this->volume,
            'taskDate' => $this->date_task,
            'taskIcon' => "http://192.168.43.74:8000/images/".$this->activity->task_icon,
            'id' => $request->idSQL
        ];
    }
}

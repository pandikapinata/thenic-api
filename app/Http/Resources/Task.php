<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Task extends JsonResource
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
            'user' => $this->user_id,
            'activityId' => $this->activity_id,
            'taskName' => $this->activity->name,
            'taskNote' => $this->note,
            'taskSets' => $this->sets,
            'taskReps' => $this->repetition,
            'taskVolume' => $this->volume,
            'taskDate' => $this->date_task,
            'taskIcon' => $this->activity->task_icon
        ];
    }
}

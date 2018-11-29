<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{

    public function task()
    {
        return $this->hasMany(Task::class);
    }
}

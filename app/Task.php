<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['id','user_id','activity_id', 'note', 'sets', 'repetition', 'volume', 'date_task'];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}

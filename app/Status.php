<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'status';
    
    public function events()
    {
        return $this->hasMany('App\Event', 'status_id');
    }
    public function eventtypes()
    {
        return $this->belongsToMany('App\Eventtype');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public function events()
    {
        return $this->hasMany('App\Event');
    }
    public function eventtypes()
    {
        return $this->belongsToMany('App\Eventtype');
    }
}

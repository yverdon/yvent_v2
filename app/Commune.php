<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    public function events()
    {
        return $this->hasMany('App\Event');
    }
    public function eventtypes()
    {
        return $this->belongsToMany('App\Eventtype', 'eventtype_commune');
    }
}

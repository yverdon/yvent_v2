<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Slottype extends Model
{
    public function slots()
    {
        return $this->hasMany('App\Slot');
    }
    public function eventtypes()
    {
        return $this->belongsToMany('App\Eventtype');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    protected $primaryKey = 'numero';

    public function events()
    {
        return $this->belongsToMany('App\Event');
    }
}

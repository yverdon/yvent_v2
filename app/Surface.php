<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Surface extends Model
{
    protected $table = 'qg_cctech.travaux_surface';
    
    public function event()
    {
        // return $this->belongsTo('App\Event', 'id', 'yvent_id');
        return $this->belongsTo('App\Event');
    }
}
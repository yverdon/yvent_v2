<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class URBs_planif_responsable extends Model
{
    protected $table = 'qg_urb.urbs_planif_responsable';
    
    public function projects()
    {
        return $this->hasMany('App\URB_planif_project', 'code', 'responsable_id');
    }
}

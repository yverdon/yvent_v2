<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'main.role';
    protected $primaryKey = 'name';

    public $incrementing = false;
    
    public function eventtypes()
    {
        return $this->belongsToMany('App\Eventtype', 'eventtype_role', 'role_id', 'eventtype_id')->withPivot('rights');
    }
    public function users()
    {
        return $this->hasMany('App\User', 'role');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Eventtype extends Model
{
    public function events()
    {
        return $this->hasMany('App\Event');
    }
    public function services()
    {
        return $this->belongsToMany('App\Service');
    }
    public function status()
    {
        return $this->belongsToMany('App\Status');
    }
    public function slottypes()
    {
        return $this->belongsToMany('App\Slottype');
    }
    public function roles()
    {
        return $this->belongsToMany('App\Role', 'eventtype_role', 'eventtype_id', 'role_id')->withPivot('rights');
    }
    public function communes()
    {
        return $this->belongsToMany('App\Commune', 'eventtype_commune');
    }
    
    public function nameWithLabel()
    {
        if (isset($this->label)) {
            return $this->label . ' ' . $this->name;
        }
        return $this->name;
    }
    
    public function namePluralWithLabel()
    {
        if (isset($this->label)) {
            return $this->label . ' ' . $this->name_plural;
        }
        return $this->name_plural;
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    public function eventtype()
    {
        return $this->belongsTo('App\Eventtype');
    }

    public function service()
    {
        return $this->belongsTo('App\Service');
    }

    public function commune()
    {
        return $this->belongsTo('App\Commune');
    }

    public function status()
    {
        return $this->belongsTo('App\Status', 'status_id');
    }

    public function slots()
    {
        return $this->hasMany('App\Slot');
    }

    public function documents()
    {
        return $this->hasMany('App\Document');
    }

    public function logs()
    {
        return $this->hasMany('App\Log');
    }

    public function surface()
    {
        return $this->hasOne('App\Surface');
    }

    public function partners()
    {
        return $this->belongsToMany('App\Service');
    }

    public function investments()
    {
        return $this->belongsToMany('App\Investment');
    }

    public function getStartingDateAttribute($value)
    {
        return $this->slots->min('start_time');
    }

    public function getEndingDateAttribute($value)
    {
        return $this->slots->max('end_time');
    }

    // use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    // protected $dates = ['deleted_at'];
}

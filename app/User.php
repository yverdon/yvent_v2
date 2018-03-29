<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Notifications\ResetPassword2 as ResetPasswordNotification;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // 'name', 'email', 'password',
        'username', 'email', 'password', 'role', 'key'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    // protected $connection = 'bdtest';
    
    public function role_rel()
    {
        return $this->belongsTo('App\Role', 'role');
    }
    
    public function isAdmin()
    {
        if ($this->role == 'role_admin')
            {
                return true;
            }

        return false;
    } 
    
    public function eventtypesReadable()
    {
        return Eventtype::whereHas('roles',function($query) {
                                                                $query
                                                                    ->where('name','=',$this->role)
                                                                    ->whereIn('rights',['R','RW']);
                                                            })  ->orderby("id")
                                                                ->get();
    }
    
    public function statusReadable()
    {
        return Status::whereHas('eventtypes',function($query) {
                                                                $query
                                                                    ->whereHas('roles',function($query) {
                                                                                                            $query
                                                                                                                ->where('name','=',$this->role)
                                                                                                                ->whereIn('rights',['R','RW']);
                                                                                                        });
                                                                })
                                                                ->orderby("id")
                                                                ->get();
    }
    
    public function eventtypesWriteable()
    {
        return Eventtype::whereHas('roles',function($query) {
                                                                $query
                                                                    ->where('name','=',$this->role)
                                                                    ->where('rights','=','RW');
                                                            })  ->orderby("id")
                                                                ->get();
    }
    
    public function eventtypesWriteable2()
    {
        return Eventtype::whereHas('roles',function($query) {
                                                                $query
                                                                    ->where('name','=',$this->role)
                                                                    ->where('rights','=','RW');
                                                            })  ->orderby("id");
    }
    
    public function isReader()
    {
        if ($this->eventtypesReadable()->count() > 0)
            {
                return true;
            }

        return false;
    }
    
    public function isEditor()
    {
        if ($this->eventtypesWriteable()->count() > 0)
            {
                return true;
            }

        return false;
    }
    
    public function isEventReader($eventtype_id)
    {
        $eventtypes_list = $this->eventtypesReadable()->pluck('id')->toArray();
        
        if (in_array($eventtype_id, $eventtypes_list))
        {
            return true;
        }
        
        return false;
    }
    
    public function isEventEditor($eventtype_id)
    {
        $eventtypes_list = $this->eventtypesWriteable()->pluck('id')->toArray();
        
        if (in_array($eventtype_id, $eventtypes_list))
        {
            return true;
        }
        
        return false;
    }
    
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}

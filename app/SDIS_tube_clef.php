<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SDIS_tube_clef extends Model
{
    protected $table = 'mf_sdis.sdis_tube_clef';
    
    public function type()
    {
        return $this->belongsTo('App\SDISs_type_tube', 'type_tube_id', 'id');
    }
    
    public function tube_photos()
    {
        return $this->hasMany('App\SDIS_doc', 'var', 'id')->where('type', 'tube');
    }
    
    public function content_photos()
    {
        return $this->hasMany('App\SDIS_doc', 'var', 'id')->where('type', 'contenu');
    }
    
    public function receipts()
    {
        return $this->hasMany('App\SDIS_doc', 'var', 'id')->where('type', 'quittance');
    }
}

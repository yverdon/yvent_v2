<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class POLADM_etablissement extends Model
{
    protected $table = 'mf_poladm.poladm_etablissement';
    
    public function type()
    {
        return $this->belongsTo('App\POLADMs_type_etablissement', 'type_etablissement_id', 'id');
    }
}

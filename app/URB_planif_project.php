<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class URB_planif_project extends Model
{
    protected $table = 'qg_urb.urb_planif_projet';
    
    public function responsable()
    {
        return $this->belongsTo('App\URBs_planif_responsable', 'responsable_id', 'code');
    }
    
    public function outil()
    {
        return $this->belongsTo('App\URBs_planif_outil_plani', 'outil_plani_id', 'code');
    }
    
    public function etat()
    {
        return $this->belongsTo('App\URBs_planif_etat_plani', 'etat_plani_id', 'code');
    }
}

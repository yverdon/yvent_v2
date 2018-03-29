<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    public function event()
    {
        return $this->belongsTo('App\Event');
    }
    
    public function slottype()
    {
        return $this->belongsTo('App\Slottype');
    }
    
    public function color1()
    {
        // Standard
        if (!$this->slottype->inverse_colors) {
            $color = $this->event->Status->color1;
        }
        //  Couleurs négatives (montage, démontage)
        else {
            $color = "#fcfcfc";
        }
        // Couleurs définies au niveau du slottype
        ($this->slottype->color1 <> '' ? $color = $this->slottype->color1 : '');
        return $color;
    }
    
    public function color2()
    {
        // Standard
        if (!$this->slottype->inverse_colors) {
            $color = $this->event->Status->color2;
        }
        //  Couleurs négatives (montage, démontage)
        else {
            $color = $this->event->Status->color2;
        }
        // Couleurs définies au niveau du slottype
        ($this->slottype->color2 <> '' ? $color = $this->slottype->color2 : '');
        return $color;
    }
    
    public function color3()
    {
        // Standard
        if (!$this->slottype->inverse_colors) {
            $color = $this->event->Status->color3;
        }
        //  Couleurs négatives (montage, démontage)
        else {
            $color = $this->event->Status->color2;
        }
        // Couleurs définies au niveau du slottype
        ($this->slottype->color3 <> '' ? $color = $this->slottype->color3 : '');
        return $color;
    }
}

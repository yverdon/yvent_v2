<?php

namespace App\Helpers;
date_default_timezone_set('Europe/Zurich');

use PDF;
use DateTime;

class PdfHelper {
    
    /* PDF constants */
    static $linespace = 5;
    static $smallfontsize = 7;
    static $fontsize = 9;
    static $marginleft = 20;
    static $margintopheader = 10;
    static $margintop = 20;
    static $linestylewhite = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 255, 255));
    static $linestylegrey = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(100, 100, 100));
    static $linestylered = array('width' => 1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 0, 0));
    static $labelbackgroundcolor = array(245, 245, 245);
    static $labeltextcolor = array(100, 100, 100);
    static $valuebackgroundcolor = array(225, 225, 225);
    
    static $currentline = 0;

    /* Date validation */
    static function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    
    /* Date-time validation */
    static function validateDateTime($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    
    /* Standard cell function */
    static function cell($lineoffset, $col, $width1, $width2, $label, $value = false, $height = 1, $unit = false, $ishtml = false)
    {
        self::$currentline = self::$currentline + $lineoffset;
        PDF::SetLineStyle(self::$linestylewhite);
        if (is_bool($value)) {
            $value = $value ? 'Oui' : 'Non';
            // $value = 'b:'. $value;
        }
        // if (is_string($value)) {
            // $value = 's:'. $value;
        // }
        // Date
        if (self::validateDate($value) OR self::validateDateTime($value)) {
            $value = date("d.m.Y", strtotime($value));
        }
        // Nombre réel
        elseif (is_float($value + 0)) {
            $value = number_format($value, 2, '.', "'");
        }
        // Vide
        if ((is_numeric($value) and $value == 0) or $value == '') {
            $value = '-';
        }
        if ($unit) {
            $value = $value . ' ' . $unit;
        }
        if ($width1 <> 0) {
            PDF::SetTextColor(100, 100, 100);
            PDF::SetFont('helvetica', '', self::$smallfontsize);
            PDF::SetFillColorArray(self::$labelbackgroundcolor);
            PDF::MultiCell($width1, self::$linespace * $height, $label, 1, 'L', 1, 2, $col + self::$marginleft, self::$currentline * self::$linespace + self::$margintop, true, 0, $ishtml);
        }
        if ($width2 <> 0) {
            PDF::SetTextColor(0, 0, 0);
            PDF::SetFont('helvetica', 'B', self::$fontsize);
            if ($value === '-') {
                PDF::SetTextColor(100, 100, 100);
                PDF::SetFont('helvetica', '', self::$smallfontsize);
            }
            PDF::SetFillColorArray(self::$valuebackgroundcolor);
            PDF::MultiCell($width2, self::$linespace * $height, trim($value), 1, 'L', 1, 2, $col + self::$marginleft + $width1, self::$currentline * self::$linespace + self::$margintop, true, 0, $ishtml);
            ( $label <> '' ? self::$currentline = self::$currentline + $height - 1 : '');
        }
    }
    
    /* Pie Chart function */
    static function pie($lineoffset, $col, $width1, $width2, $label = 'Test', $values = ['Value 1' => ['val' => 100, 'r'=> 86, 'g' => 172, 'b' => 66],'Value 2'=> ['val' => 25, 'r'=> 226, 'g' => 0, 'b' => 69],'Value 3' => ['val' => 75, 'r'=> 0, 'g' => 118, 'b' => 189]], $radius = 15)
    {
        $sum = array_sum(array_column($values, 'val'));
        if ($sum > 0) {
            $height = 2 * $radius / self::$linespace;
            
            self::$currentline = self::$currentline + $lineoffset;
            PDF::SetLineStyle(self::$linestylewhite);
            PDF::SetTextColor(100, 100, 100);
            PDF::SetFont('helvetica', '',self::$smallfontsize);
            PDF::SetFillColorArray(self::$labelbackgroundcolor);
            PDF::MultiCell($width1, self::$linespace * $height, $label, 1, 'L', 1, 2, $col + self::$marginleft, self::$currentline * self::$linespace + self::$margintop);
            PDF::SetTextColor(0, 0, 0);
            PDF::SetFont('helvetica', 'B',self::$fontsize);
            PDF::SetFillColorArray(self::$valuebackgroundcolor);
            PDF::MultiCell($width2, self::$linespace * $height, '', 1, 'L', 1, 2, $col + self::$marginleft + $width1, self::$currentline * self::$linespace + self::$margintop);
            
            $angle1 = 0;
            $angle2 = 0;
            $i = 0;
            
            foreach ($values as $key => $value){
                $angle1 = $angle2;
                $angle2 += $value['val'] / $sum * 360;
                PDF::SetFillColor($value['r'], $value['g'], $value['b']);
                PDF::PieSector(PdfHelper::$marginleft + $radius + $width1, PdfHelper::$currentline * PdfHelper::$linespace + PdfHelper::$margintop + $radius, $radius, $angle1, $angle2, 'FD', true, 90, 2);
                PDF::SetTextColor($value['r'], $value['g'], $value['b']);
                PDF::Text(PdfHelper::$marginleft + 2 * $radius + $width1, (PdfHelper::$currentline + $i) * PdfHelper::$linespace + PdfHelper::$margintop , $key . ': ' . round($value['val'] / $sum * 100, 1) . ' %');
                $i++;
            }
        PdfHelper::$currentline = PdfHelper::$currentline + $height - 1;
        
        }
    }
    
    /* Title with line function */
    static function title($label)
    {
        self::$currentline = self::$currentline + 3;
        PDF::SetLineStyle(self::$linestylegrey);
        PDF::SetTextColor(100, 100, 100);
        PDF::SetFont('helvetica', 'B', 11);
        PDF::SetFillColorArray(self::$labelbackgroundcolor);
        PDF::MultiCell(0, self::$linespace, $label, 'B', 'L', 0, 2,self::$marginleft, self::$currentline * self::$linespace + self::$margintop);
    }
    
    /* New page */
    // static function newPage()
    // {
        // PDF::AddPage();
        // self::$currentline = 0;
    // }
    
    /* New page */
    static function newPage($lines = 0)
    {
        if (self::$currentline == $lines OR $lines == 0) {
            PDF::AddPage();
            self::$currentline = 0;
        }
    }
    
    /* Set title */
    static function SetTitle($title)
    {
        PDF::SetTitle($title);
    }
    
    /* Output PDF */
    static function Output($filename)
    {
        PDF::Output($filename);
    }
    
    /* URBAT Header and footer */
    static function setUrbatHeaderFooter($title)
    {
        PDF::setHeaderCallback(function($pdf) use ($title) {
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->MultiCell(50, 10, "Service\nde l'Urbanisme\net des Bâtiments", 0, 'L', 1, 2,self::$marginleft + 13,self::$margintopheader);
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->MultiCell(100, 10, $title, 0, 'L', 1, 2,self::$marginleft + 50,self::$margintopheader);
            $pdf->ImageSVG(url('_asset') . '/YLB_Etroit_NB.svg',self::$marginleft,self::$margintopheader, 12, '', '', '', '');
        });

        PDF::setFooterCallback(function($pdf) {
            $pages = PDF::getAliasRightShift() . PDF::getAliasNumPage() . '/' . PDF::getAliasNbPages();
            $pdf->SetFont('helvetica', '',self::$fontsize);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->MultiCell(0, 10, 'Informations dépourvues de foi publique', 'T', 'L', 0, 2,self::$marginleft, 297 - 2 *self::$margintopheader);
            $pdf->MultiCell(0, 10, date("d.m.Y H:i:s"), 'T', 'C', 0, 2,self::$marginleft, 297 - 2 *self::$margintopheader);
            $pdf->MultiCell(0, 10, $pages, 0, 'R', 0, 2, 210 - 2 *self::$marginleft - 40, 297 - 2 *self::$margintopheader);
        });
    }
    
    static function setSITHeaderFooter($title)
    {
        PDF::setHeaderCallback(function($pdf) use ($title) {
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->MultiCell(50, 10, "Système\nd'information\ndu territoire", 0, 'L', 1, 2,self::$marginleft + 13,self::$margintopheader);
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->MultiCell(100, 10, $title, 0, 'L', 1, 2,self::$marginleft + 50,self::$margintopheader);
            $pdf->ImageSVG(url('_asset') . '/YLB_Etroit_NB.svg',self::$marginleft,self::$margintopheader, 12, '', '', '', '');
        });

        PDF::setFooterCallback(function($pdf) {
            $pages = PDF::getAliasRightShift() . PDF::getAliasNumPage() . '/' . PDF::getAliasNbPages();
            $pdf->SetFont('helvetica', '',self::$fontsize);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->MultiCell(0, 10, 'Informations dépourvues de foi publique', 'T', 'L', 0, 2,self::$marginleft, 297 - 2 *self::$margintopheader);
            $pdf->MultiCell(0, 10, date("d.m.Y H:i:s"), 'T', 'C', 0, 2,self::$marginleft, 297 - 2 *self::$margintopheader);
            $pdf->MultiCell(0, 10, $pages, 0, 'R', 0, 2, 210 - 2 *self::$marginleft - 40, 297 - 2 *self::$margintopheader);
        });
    }
    
    /* SDIS Header and footer */
    static function setSdisHeaderFooter($title)
    {
        PDF::setHeaderCallback(function($pdf) use ($title) {
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->MultiCell(100, 10, $title, 0, 'L', 1, 2,self::$marginleft + 50,self::$margintopheader);
            $pdf->ImageSVG(url('_asset') . '/Logo_SDISNV.svg',self::$marginleft,self::$margintopheader, 18, '', '', '', '');
        });

        PDF::setFooterCallback(function($pdf) {
            $pages = PDF::getAliasRightShift() . PDF::getAliasNumPage() . '/' . PDF::getAliasNbPages();
            $pdf->SetFont('helvetica', '',self::$fontsize);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->MultiCell(0, 10, 'Informations dépourvues de foi publique', 'T', 'L', 0, 2,self::$marginleft, 297 - 2 *self::$margintopheader);
            $pdf->MultiCell(0, 10, date("d.m.Y H:i:s"), 'T', 'C', 0, 2,self::$marginleft, 297 - 2 *self::$margintopheader);
            $pdf->MultiCell(0, 10, $pages, 0, 'R', 0, 2, 210 - 2 *self::$marginleft - 40, 297 - 2 *self::$margintopheader);
        });
    }
    
    /* PNV Header and footer */
    static function setPNVHeaderFooter($title)
    {
        PDF::setHeaderCallback(function($pdf) use ($title) {
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->MultiCell(100, 10, $title, 0, 'L', 1, 2,self::$marginleft + 50,self::$margintopheader);
            $pdf->Image(url('_asset') . '/Logo_PNV.png',self::$marginleft,self::$margintopheader, 36, '', '', '', '');
        });

        PDF::setFooterCallback(function($pdf) {
            $pages = PDF::getAliasRightShift() . PDF::getAliasNumPage() . '/' . PDF::getAliasNbPages();
            $pdf->SetFont('helvetica', '',self::$fontsize);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->MultiCell(0, 10, 'Informations dépourvues de foi publique', 'T', 'L', 0, 2,self::$marginleft, 297 - 2 *self::$margintopheader);
            $pdf->MultiCell(0, 10, date("d.m.Y H:i:s"), 'T', 'C', 0, 2,self::$marginleft, 297 - 2 *self::$margintopheader);
            $pdf->MultiCell(0, 10, $pages, 0, 'R', 0, 2, 210 - 2 *self::$marginleft - 40, 297 - 2 *self::$margintopheader);
        });
    }
    
    /* Croix */
    static function mapcroix($imagecenterx,$imagecentery)
    {
        $radius = 3;
        PDF::SetLineStyle(self::$linestylered);
        PDF::SetAlpha(0.9);
        PDF::Line($imagecenterx - $radius, $imagecentery, $imagecenterx + $radius, $imagecentery);
        PDF::Line($imagecenterx, $imagecentery - $radius, $imagecenterx, $imagecentery + $radius);
        PDF::SetAlpha(1);
    }
    
    /* Circle */
    static function mapcircle($imagecenterx,$imagecentery)
    {
        $radius = 3;
        PDF::SetLineStyle(self::$linestylered);
        PDF::SetAlpha(0.9);
        PDF::Circle($imagecenterx, $imagecentery, $radius);
        PDF::SetAlpha(1);
    }
    
    /* Image */
    static function image($lineoffset, $col, $width, $height, $path, $croix = false)
    {
        list($imagewidth, $imageheight) = getimagesize($path);
        $imageratio = $imagewidth/$imageheight;
        if ($width/$height > $imageratio) {
           $imagewidth = $height * $imageratio;
           $imageheight = $height;
        } else {
           $imagewidth = $width;
           $imageheight = $width / $imageratio;
        }
        
        self::$currentline = self::$currentline + $lineoffset;
        PDF::Image($path, $col + self::$marginleft, self::$currentline * self::$linespace + self::$margintop, $imagewidth, $imageheight, '', '', '', false, 300);
        
        ($croix == 'circle' ? self::mapcircle($col + self::$marginleft + $imagewidth / 2 ,self::$currentline * self::$linespace + self::$margintop + $imageheight / 2) : '');
        ($croix == 'cross' ? self::mapcroix($col + self::$marginleft + $imagewidth / 2 ,self::$currentline * self::$linespace + self::$margintop + $imageheight / 2) : '');
    }
    
    /* Centered Map function */
    static function mapcenter($lineoffset, $col, $lineoffsetafter, $imagewidth, $imageheight, $croix = true, $centerx = 2539000, $centery = 1181000, $mapscale = 2000, $symbolscale = 1, $layers = 'MO_cs_surfacecs,MO_batiment,MO_od_element_surfacique,MO_bf_posimmeuble,MO_bf_bien_fonds,MO_cs_posnumero_de_batiment,MO_bf_ddp,MO_od_element_lineaire,ADR_place_,ADR_rue,ADR_adresse,MO_lim_limite_commune,MO_etiquette_commune', $imageresolution = 300, $crs = 'EPSG:2056')
    {
        self::$currentline = self::$currentline + $lineoffset;
        PDF::SetLineStyle(self::$linestylegrey);
        
        $x1 = $centerx - $imagewidth / 2 * $mapscale / 1000;
        $x2 = $centerx + $imagewidth / 2 * $mapscale / 1000;
        $y1 = $centery - $imageheight / 2 * $mapscale / 1000;
        $y2 = $centery + $imageheight / 2 * $mapscale / 1000;
        
        $mapresolution = intval($imageresolution / $mapscale * ($mapscale * $symbolscale));
        $mapwidth = $imagewidth / 10 / 2.54 * $imageresolution;
        $mapheight = $imageheight / 10 / 2.54 * $imageresolution;
        
        $imagetop = self::$currentline * self::$linespace + self::$margintop;
        $imageleft = self::$marginleft + $col;
        
        PDF::Image("https://mapnv.ch/main/wsgi/mapserv_proxy?version=1.3.0&service=WMS&request=GetMap&layers=$layers&width=$mapwidth&height=$mapheight&crs=$crs&bbox=$x1,$y1,$x2,$y2&format=image/png&map_resolution=$mapresolution", $imageleft, $imagetop, $imagewidth, $imageheight, '', '', '', false, $imageresolution,'','',false,1);
        ($lineoffsetafter ? self::$currentline = self::$currentline + intval($imageheight / self::$linespace) - 1 : '');
        ($croix ? self::mapcroix($imageleft + $imagewidth / 2, $imagetop + $imageheight / 2) : '');
    }

    /* Extent Map function */
    static function mapextent($lineoffset, $col, $lineoffsetafter, $imagewidth, $imageheight, $croix = true, $objectx1 = 2539000, $objecty1 = 1181000, $objectx2 = 541000, $objecty2 = 182000, $margin = 50, $symbolscale = 1, $layers = 'MO_cs_surfacecs,MO_batiment,MO_od_element_surfacique,MO_bf_posimmeuble,MO_bf_bien_fonds,MO_cs_posnumero_de_batiment,MO_bf_ddp,MO_od_element_lineaire,ADR_place_,ADR_rue,ADR_adresse,MO_lim_limite_commune,MO_etiquette_commune', $imageresolution = 300, $crs = 'EPSG:2056')
    {
        self::$currentline = self::$currentline + $lineoffset;
        PDF::SetLineStyle(self::$linestylegrey);

        $objectsizex = $objectx2 - $objectx1;
        $objectsizey = $objecty2 - $objecty1;
        $marginmin = ( $objectsizex / $objectsizey > $imagewidth / $imageheight ? 'H' : 'V');
        
        if ($marginmin == 'H') {
            $x1 = $objectx1 - $margin;
            $x2 = $objectx2 + $margin;
            $y1 = $objecty1 - ((($objectsizex + 2 * $margin) / $imagewidth * $imageheight) - $objectsizey) / 2;
            $y2 = $objecty2 + ((($objectsizex + 2 * $margin) / $imagewidth * $imageheight) - $objectsizey) / 2;
            $mapscale = ($objectsizex + 2 * $margin) * 1000 / $imagewidth;
        } else {
            $x1 = $objectx1 - ((($objectsizey + 2 * $margin) / $imageheight * $imagewidth) - $objectsizex) / 2;
            $x2 = $objectx2 + ((($objectsizey + 2 * $margin) / $imageheight * $imagewidth) - $objectsizex) / 2;
            $y1 = $objecty1 - $margin;
            $y2 = $objecty2 + $margin;
            $mapscale = ($objectsizey + 2 * $margin) * 1000 / $imageheight;
        }
        
        $mapresolution = intval($imageresolution / $mapscale * ($mapscale * $symbolscale));
        $mapwidth = $imagewidth / 10 / 2.54 * $imageresolution;
        $mapheight = $imageheight / 10 / 2.54 * $imageresolution;
        
        $imagetop = self::$currentline * self::$linespace + self::$margintop;
        $imageleft = self::$marginleft + $col;
        
        PDF::Image("http://mapnv.ch/main/wsgi/mapserv_proxy?version=1.3.0&service=WMS&request=GetMap&layers=$layers&width=$mapwidth&height=$mapheight&crs=$crs&bbox=$x1,$y1,$x2,$y2&format=image/png&map_resolution=$mapresolution", $imageleft, $imagetop, $imagewidth, $imageheight, '', '', '', false, $imageresolution,'','',false,1);
        ($lineoffsetafter ? self::$currentline = self::$currentline + intval($imageheight / self::$linespace) - 1 : '');
        ($croix ? self::mapcroix($imageleft + $imagewidth / 2, $imagetop + $imageheight / 2) : '');
    }
    
    static function dateFormat($date)
    {
        return is_null($date) ? "": date("d.m.Y", strtotime($date));
    }

}
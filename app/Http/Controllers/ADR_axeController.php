<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use PDF;
use App\Helpers\PdfHelper;
use App\ADR_axe;

class ADR_axeController extends Controller
{
    public function index()
    {
        /* Database queries */
        $rues = ADR_axe::where('typeaxe','=',1)->orderBy('ruenomcomp3')->get();
        $filename = 'Liste_des_rues_Yverdon_' . date("Ymd_His") . '.pdf';
        
        /**
         * PDF Build.
         */
        
        /* Set Title */
        PdfHelper::SetTitle($filename);
        
        /* Set Header and Footer */
        PdfHelper::setSITHeaderFooter('Liste des rues');
        
        /* Set Content - page 1 */
        PdfHelper::newPage();
        
        // PdfHelper::title('Coordonnées');
        
        foreach ($rues as $rue)
        {
            PdfHelper::cell(1,0,30,80,$rue->rueid,$rue->ruenomcomp3,1,false,true);
            PdfHelper::cell(0,0,110,70,$rue->rueid,'<a href="http://mapnv.ch/theme/plan_de_ville?wfs_layer=ADR_axe&wfs_id='. $rue->rueid .'" target="_blank">Carte</a>',1,false,true);
            PdfHelper::newPage(50);
        }
        
        PdfHelper::Output($filename);
    }
}

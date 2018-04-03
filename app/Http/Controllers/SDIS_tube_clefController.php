<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use PDF;
use App\Helpers\PdfHelper;
use App\SDIS_tube_clef;

class SDIS_tube_clefController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth');
    }
    
    public function show($id)
    {            
        /* Database queries */
        $tube = SDIS_tube_clef::where('id',$id)->first();
        $filename = 'Fiche_' . $tube->id . '_' . date("Ymd_His") . '.pdf';
        $title = $tube->nom;
        $extent = DB::select('select public.ST_X(geom) as x, public.ST_Y(geom) as y from mf_sdis.sdis_tube_clef where id = ?', [$tube->id])[0];
        
        /**
         * PDF Build.
         */
        
        /* Set Title */
        PdfHelper::SetTitle($filename);
        
        /* Set Header and Footer */
        PdfHelper::setSdisHeaderFooter('Tube à clé: ' . $tube->nom);
        
        /* Set Content - page 1 */
        PdfHelper::newPage();
        
        PdfHelper::title('Informations générales');
        
        PdfHelper::cell(2,0,40,140,'Adresse',$tube->adresse1);
        PdfHelper::cell(1,40,0,140,'',$tube->adresse2);
        
        PdfHelper::cell(2,0,40,40,'Nombre de clé',$tube->nbre_clef);
        PdfHelper::cell(0,100,40,40,'Dossier intervention',$tube->dossier_intervention);
        PdfHelper::cell(1,0,40,40,'Modèle/état',$tube->type->name);
        PdfHelper::cell(0,100,40,40,'Sous alarme',$tube->detection);
        PdfHelper::cell(1,0,40,40,'Numéro',$tube->id);
        PdfHelper::cell(0,100,40,40,'Clé(s) contrôlée(s)',$tube->controle_clef);
        
        PdfHelper::cell(2,0,40,140,'Remarques',$tube->remarque,3);
        
        PdfHelper::title('Carte et photos');
        
        PdfHelper::mapcenter(2,0,true,180,80,true,$extent->x,$extent->y,1000);

        if($tube->tube_photos->count() > 0) {
            PdfHelper::image(2,0,90,70,$this->change_path($tube->tube_photos->first()->path), 'circle');
        }
        if($tube->content_photos->count() > 0) {
            PdfHelper::image(0,90,90,70,$this->change_path($tube->content_photos->first()->path));
        }
        PdfHelper::Output($filename);
    }
    
    public function change_path($path)
    {
        return str_replace("/main/wsgi","/var/sig/yverdon_geoportail",$path);
    }
}

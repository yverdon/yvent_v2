<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use PDF;
use App\Helpers\PdfHelper;
use App\POLADM_etablissement;


class POLADM_etablissementController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth');
    }
    
    public function show($id)
    {            
        /* Database queries */
        $etablissement = POLADM_etablissement::where('id',$id)->first();
        $filename = 'Fiche_' . $etablissement->nom_abrege . '_' . date("Ymd_His") . '.pdf';
        $title = $etablissement->nom;
        $extent = DB::select('select public.ST_X(geom) as x, public.ST_Y(geom) as y from mf_poladm.poladm_etablissement where id = ?', [$etablissement->id])[0];
        
        /**
         * PDF Build.
         */
        
        /* Set Title */
        PdfHelper::SetTitle($filename);
        
        /* Set Header and Footer */
        PdfHelper::setPNVHeaderFooter('Établissement public: ' . $etablissement->nom);
        
        /* Set Content - page 1 */
        PdfHelper::newPage();
        
        PdfHelper::title('Coordonnées');
        
        PdfHelper::cell(2,0,30,150,'Nom',$etablissement->nom);
        PdfHelper::cell(1,0,30,150,'Nom abrégé',$etablissement->nom_abrege);
        PdfHelper::cell(1,0,30,150,'Adresse',$etablissement->adresse);
        PdfHelper::cell(1,0,30,50,'Case postale',$etablissement->case_postale);
        PdfHelper::cell(1,0,30,50,'Téléphone',$etablissement->telephone);
        PdfHelper::cell(0,100,30,50,'Email',$etablissement->email);
        
        PdfHelper::cell(2,0,30,150,'Titulaire',$etablissement->nom_titulaire);
        PdfHelper::cell(1,0,30,50,'Téléphone',$etablissement->tel_titulaire);
        PdfHelper::cell(1,0,30,150,'Titulaire',$etablissement->nom_titulaire2);
        PdfHelper::cell(1,0,30,50,'Téléphone',$etablissement->tel_titulaire2);
        
        PdfHelper::title('Caractéristiques');
        
        PdfHelper::cell(2,0,30,50,'Type',$etablissement->type->name);
        PdfHelper::cell(0,100,30,50,'Dernière vérification',$etablissement->derniere_verification);
        
        PdfHelper::cell(1,0,30,50,'Ouvert',$etablissement->ouvert);
        PdfHelper::cell(0,100,30,50,'Dossier',$etablissement->dossier);
        PdfHelper::cell(1,0,30,150,'Remarques',$etablissement->remarque,2);
        
        PdfHelper::cell(1,0,30,50,'Terrasse',$etablissement->terrasse);
        PdfHelper::cell(1,0,30,150,'Remarques terrasse',$etablissement->terrasse_remarque,2);
        PdfHelper::cell(1,0,30,50,'Musique',$etablissement->musique);
        PdfHelper::cell(1,0,30,150,'Remarques musique',$etablissement->musique_remarque,2);
        PdfHelper::cell(2,0,30,150,'Horaires',$etablissement->horaire,2);
        
        // PdfHelper::title('Carte');
        PdfHelper::mapcenter(2,0,true,180,80,true,$extent->x,$extent->y,1000,1,'MO_cs_surfacecs,MO_batiment,MO_od_element_surfacique,MO_bf_bien_fonds,MO_od_element_lineaire,ADR_place_,ADR_rue,ADR_adresse,MO_lim_limite_commune,MO_etiquette_commune');

        PdfHelper::Output($filename);
    }
}

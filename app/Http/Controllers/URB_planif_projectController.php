<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use PDF;
use App\Helpers\PdfHelper;
use App\URB_planif_project;


class URB_planif_projectController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth');
    }
    
    public function show($id)
    {            
        /* Database queries */
        $project = URB_planif_project::where('id',$id)->first();
        $filename = 'Fiche_' . $project->nom . '_' . date("Ymd_His") . '.pdf';
        $title = $project->nom;
        $extent = DB::select('select public.ST_XMin(geom) as x1, public.ST_YMin(geom) as y1, public.ST_XMax(geom) as x2, public.ST_YMax(geom) as y2 from qg_urb.urb_planif_projet where id = ?', [$project->id])[0];
        
        /**
         * PDF Build.
         */
        
        /* Set Title */
        PdfHelper::SetTitle($filename);
        
        /* Set Header and Footer */
        PdfHelper::setUrbatHeaderFooter('Fiche de projet: ' . $project->nom);
        
        /* Set Content - page 1 */
        PdfHelper::newPage();
        
        PdfHelper::title('Informations générales');
        
        PdfHelper::cell(2,0,80,100,'Nom du projet',$project->nom);
        PdfHelper::cell(1,0,80,100,'Responsable',(count($project->responsable) ? $project->responsable->description_f : ''));
        PdfHelper::cell(1,0,80,100,'Dernière modification',$project->date_modified);
        
        PdfHelper::mapextent(2,140,false,40,40,true,$extent->x1,$extent->y1,$extent->x2,$extent->y2,2000,1,'MO_lim_limite_commune,MO_etiquette_commune');
        PdfHelper::mapextent(0,0,true,139,80,false,$extent->x1,$extent->y1,$extent->x2,$extent->y2,50,0.5,'MO_cs_surfacecs,MO_batiment,MO_od_element_surfacique,MO_bf_posimmeuble,MO_bf_bien_fonds,MO_cs_posnumero_de_batiment,MO_bf_ddp,MO_od_element_lineaire,ADR_place_,ADR_rue,ADR_adresse,MO_lim_limite_commune,MO_etiquette_commune,URB_planif_project_public');
        
        PdfHelper::title('Procédure');
        
        PdfHelper::cell(2,0,80,100,'Outil de planification',(count($project->outil) ? $project->outil->description_f : ''));
        PdfHelper::cell(1,0,80,100,'État de la procédure',(count($project->etat) ? $project->etat->description_f : ''));

        PdfHelper::cell(2,80,40,0,'Date');
        PdfHelper::cell(0,120,40,0,'Horizon');
        PdfHelper::cell(1,0,80,40,'Début des réflexions / études',$project->date_debut_etude);
        PdfHelper::cell(0,120,0,40,'','');
        PdfHelper::cell(1,0,80,40,'Validation par la Municipalité',$project->date_validation_muni);
        PdfHelper::cell(0,120,0,40,'',$project->hori_validation_muni);
        PdfHelper::cell(1,0,80,40,'1er envoi à l\'examen préalable',$project->date_exa_prealable);
        PdfHelper::cell(0,120,0,40,'',$project->hori_exa_prealable);
        PdfHelper::cell(1,0,80,40,'Début de l\'enquête publique',$project->date_debut_enquete);
        PdfHelper::cell(0,120,0,40,'',$project->hori_debut_enquete);
        PdfHelper::cell(1,0,80,40,'Adoption par le Conseil communal',$project->date_adoption_cc);
        PdfHelper::cell(0,120,0,40,'',$project->hori_adoption_cc);
        PdfHelper::cell(1,0,80,40,'Approbation par le Conseil d\'État',$project->date_adoption_ce);
        PdfHelper::cell(0,120,0,40,'',$project->hori_adoption_ce);
        PdfHelper::cell(1,0,80,40,'Début des travaux',$project->date_debut_travaux);
        PdfHelper::cell(0,120,0,40,'',$project->hori_debut_travaux);
        
        PdfHelper::cell(2,0,80,100,'Remarques sur la procédure',$project->remarque_procedure,5);
        
         /* Set Content - page 2 */
        PdfHelper::newPage();
        
        PdfHelper::title('Statistiques');
        
        PdfHelper::cell(2,0,80,30,'Surface du périmètre',$project->surface,1,'m²');
        PdfHelper::cell(1,0,80,30,'Surface du périmètre hors DP',$project->surface_hors_dp,1,'m²');
        PdfHelper::cell(1,0,80,30,'Surface constructible',$project->surface_constructible,1,'m²');
        PdfHelper::cell(1,0,80,30,'Surface propriété de la commune',$project->surface_ylb,1,'m²');
        // PdfHelper::cell(0,120,30,30,'Ratio surface %',$project->ratio_surface_ylb);
        
        PdfHelper::pie(2, 0, 80, 100, 'Parts des propriétaires fonciers (hors DP)', ['Commune' => ['val' => $project->surface_ylb, 'r'=> 86, 'g' => 172, 'b' => 66],'Autres'=> ['val' => $project->surface_hors_dp - $project->surface_ylb, 'r'=> 226, 'g' => 0, 'b' => 69]],12);
        
        PdfHelper::cell(2,80,30,0,'Nombre');
        PdfHelper::cell(1,0,80,30,'Potentiel habitants',$project->potentiel_habitants);
        PdfHelper::cell(1,0,80,30,'Potentiel emplois',$project->potentiel_emplois);
        PdfHelper::cell(1,0,80,30,'Potentiel total',$project->total_potentiel_hab_emplois);
        
        PdfHelper::pie(2, 0, 80, 100, 'Parts habitants/emplois', ['Habitants' => ['val' => $project->potentiel_habitants, 'r'=> 0, 'g' => 118, 'b' => 189],'Emplois'=> ['val' => $project->potentiel_emplois, 'r'=> 0, 'g' => 93, 'b' => 146]],12);
        
        PdfHelper::cell(2,0,80,100,'Remarques sur les statistiques',$project->remarque_statistique,5);
        
        // PdfHelper::cell(3,0,80,100,'Parcelle(s) et DP concernés par le projet',$project->numero_bf,5);
        
        /* Set Content - page 3 */
        // PdfHelper::newPage();
        
        // PdfHelper::title('PGA / SDA actuel');
        
        // PdfHelper::cell(2,0,80,100,'Surface en zone à bâtir (15 LAT)',$project->zab_actuel,5);
        // PdfHelper::cell(1,0,80,100,'Surface hors zone à bâtir (17 ou 18 LAT)',$project->hzab_actuel,5);
        // PdfHelper::cell(1,0,80,100,'Surface en zone agricole (16 LAT) ou forêt',$project->za_actuel,5);
        // PdfHelper::cell(1,0,80,100,'Surface en SDA',$project->surface_sda_actuel);
        
        /* Set Content - page 4 */
        PdfHelper::newPage();
        
        PdfHelper::title('Affectations et SDA avant et après projet');
        
        PdfHelper::cell(2,0,80,0,'État avant projet');
        PdfHelper::cell(0,80,80,0,'État après projet');
        PdfHelper::cell(2,0,160,0,'Surface en zone à bâtir (15 LAT)');
        PdfHelper::cell(1,0,60,0,'Nom zone');
        PdfHelper::cell(0,60,20,0,'Surface [m²]');
        PdfHelper::cell(0,80,60,0,'Nom zone');
        PdfHelper::cell(0,140,20,0,'Surface [m²]');
        PdfHelper::cell(1,0,0,60,'',$project->zab_ancien_1);
        PdfHelper::cell(0,60,0,20,'',$project->surface_zab_ancien_1);
        PdfHelper::cell(0,80,0,60,'',$project->zab_nouveau_1);
        PdfHelper::cell(0,140,0,20,'',$project->surface_zab_nouveau_1);
        PdfHelper::cell(1,0,0,60,'',$project->zab_ancien_2);
        PdfHelper::cell(0,60,0,20,'',$project->surface_zab_ancien_2);
        PdfHelper::cell(0,80,0,60,'',$project->zab_nouveau_2);
        PdfHelper::cell(0,140,0,20,'',$project->surface_zab_nouveau_2);
        PdfHelper::cell(1,0,0,60,'',$project->zab_ancien_3);
        PdfHelper::cell(0,60,0,20,'',$project->surface_zab_ancien_3);
        PdfHelper::cell(0,80,0,60,'',$project->zab_nouveau_3);
        PdfHelper::cell(0,140,0,20,'',$project->surface_zab_nouveau_3);
        PdfHelper::cell(0,160,20,0,'Gain+/perte-');
        PdfHelper::cell(1,40,20,20,'Total',$project->surface_zab_ancien_total);
        PdfHelper::cell(0,120,20,20,'Total',$project->surface_zab_nouveau_total);
        PdfHelper::cell(0,160,0,20,'',$project->diff_zab);
        
        PdfHelper::cell(2,0,160,0,'Surface en zone à protéger ou autres (17 ou 18 LAT)');
        PdfHelper::cell(1,0,60,0,'Nom zone');
        PdfHelper::cell(0,60,20,0,'Surface [m²]');
        PdfHelper::cell(0,80,60,0,'Nom zone');
        PdfHelper::cell(0,140,20,0,'Surface [m²]');
        PdfHelper::cell(1,00,0,60,'',$project->hzab_ancien_1);
        PdfHelper::cell(0,60,0,20,'',$project->surface_hzab_ancien_1);
        PdfHelper::cell(0,80,0,60,'',$project->hzab_nouveau_1);
        PdfHelper::cell(0,140,0,20,'',$project->surface_hzab_nouveau_1);
        PdfHelper::cell(1,0,0,60,'',$project->hzab_ancien_2);
        PdfHelper::cell(0,60,0,20,'',$project->surface_hzab_ancien_2);
        PdfHelper::cell(0,80,0,60,'',$project->hzab_nouveau_2);
        PdfHelper::cell(0,140,0,20,'',$project->surface_hzab_nouveau_2);
        PdfHelper::cell(1,0,0,60,'',$project->hzab_ancien_3);
        PdfHelper::cell(0,60,0,20,'',$project->surface_hzab_ancien_3);
        PdfHelper::cell(0,80,0,60,'',$project->hzab_nouveau_3);
        PdfHelper::cell(0,140,0,20,'',$project->surface_hzab_nouveau_3);
        PdfHelper::cell(0,160,20,0,'Gain+/perte-');
        PdfHelper::cell(1,40,20,20,'Total',$project->surface_hzab_ancien_total);
        PdfHelper::cell(0,120,20,20,'Total',$project->surface_hzab_nouveau_total);
        PdfHelper::cell(0,160,0,20,'',$project->diff_hzab);
        
        PdfHelper::cell(2,0,160,0,'Surface en zone agricole (16 LAT) ou forêt');
        PdfHelper::cell(1,0,60,0,'Nom zone');
        PdfHelper::cell(0,60,20,0,'Surface [m²]');
        PdfHelper::cell(0,80,60,0,'Nom zone');
        PdfHelper::cell(0,140,20,0,'Surface [m²]');
        PdfHelper::cell(1,0,0,60,'',$project->za_ancien_1);
        PdfHelper::cell(0,60,0,20,'',$project->surface_za_ancien_1);
        PdfHelper::cell(0,80,0,60,'',$project->za_nouveau_1);
        PdfHelper::cell(0,140,0,20,'',$project->surface_za_nouveau_1);
        PdfHelper::cell(1,0,0,60,'',$project->za_ancien_2);
        PdfHelper::cell(0,60,0,20,'',$project->surface_za_ancien_2);
        PdfHelper::cell(0,80,0,60,'',$project->za_nouveau_2);
        PdfHelper::cell(0,140,0,20,'',$project->surface_za_nouveau_2);
        PdfHelper::cell(1,0,0,60,'',$project->za_ancien_3);
        PdfHelper::cell(0,60,0,20,'',$project->surface_za_ancien_3);
        PdfHelper::cell(0,80,0,60,'',$project->za_nouveau_3);
        PdfHelper::cell(0,140,0,20,'',$project->surface_za_nouveau_3);
        PdfHelper::cell(0,160,20,0,'Gain+/perte-');
        PdfHelper::cell(1,40,20,20,'Total',$project->surface_za_ancien_total);
        PdfHelper::cell(0,120,20,20,'Total',$project->surface_za_nouveau_total);
        PdfHelper::cell(0,160,0,20,'',$project->diff_za);
        
        PdfHelper::cell(2,0,160,0,'Surface en SDA');
        PdfHelper::cell(0,60,20,0,'Surface [m²]');
        PdfHelper::cell(0,140,20,0,'Surface [m²]');
        PdfHelper::cell(0,160,20,0,'Gain+/perte-');
        PdfHelper::cell(1,60,0,20,'',$project->surface_sda_ancien);
        PdfHelper::cell(0,140,0,20,'',$project->surface_sda_nouveau);
        PdfHelper::cell(0,160,0,20,'',$project->diff_sda);
        
        PdfHelper::cell(2,0,80,100,'Remarques sur les affectations',$project->remarque_zone,5);

        PdfHelper::Output($filename);
    }
    
    public function show2($id)
    {
        $project = URB_planif_project::where('id',$id)->first();
        
        $page_title = 'Projet: ' . $project->nom;
        
        $data = [
            'page_title'    => $page_title,
            'project'       => $project 
        ];
        
        $view = \View::make('URB_planif_project', $data);
        $html = $view->render();
        
        PDF::SetTitle('Hello World');
        PDF::AddPage();
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::Output('hello_world.pdf');
    }
}

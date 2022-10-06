<?php

namespace App\Http\Controllers\AgentFormation;

use AgentPlans;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\AgentFormation;
use App\Models\Structures;
use App\Models\Level;
use App\Models\MiseEnStage;
use App\Models\RetourDeStage;
use App\Models\AgentPlan;
use phpDocumentor\Reflection\PseudoTypes\False_;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \PhpOffice\PhpSpreadsheet\Calculation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class AgentFormationController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function ordonnerAnnee($annee){
        $a = array();
        foreach ($annee as $key => $value) {
            $a[] = $value;
        }
        $a = array_unique($a);
        sort($a);
        return collect($a);
    }

    public function index()
    {
    	$agents = AgentFormation::all();
        $ann = collect();
        $aggV = DB::table('mise_en_stages')->select('annee_stage')->distinct('annee_stage')->get();
        $aggA = DB::table('retour_de_stages')->select('annee_rs')->distinct('annee_rs')->get();  
        foreach ($aggV as $val) {
            if($val->annee_stage != "null") $ann[] = $val->annee_stage;
        }   
        foreach ($aggA as $val) {
            if($val->annee_rs != "null") $ann[] = $val->annee_rs;
        } 

        $ann = $this->ordonnerAnnee($ann);
    	return view('admin.agentFormation.agentFormation',compact('agents','ann'));
    }

    
    public function form()
    {
        $id_status =  DB::table('type')->select('id')->where('wording','Statut')->first();
        $status = Level::where('id_type',$id_status->id)->get();
        $id_cat =  DB::table('type')->select('id')->where('wording','Categorie')->first();
        $categories = Level::where('id_type',$id_cat->id)->get();
        $id_corps = DB::table('type')->select('id')->where('wording','Corps de la fonction publique')->first();
        $corps = Level::where('id_type',$id_corps->id)->get();
        $id_structure =  DB::table('type')->select('id')->where('wording','Structure')->first();
        $structures = Level::where('id_type',$id_structure->id)->get();
        $id_sexe =  DB::table('type')->select('id')->where('wording','Sexe')->first();
        $sexes = Level::where('id_type',$id_sexe->id)->get();
        $plan_formations = DB::table('plan_formations')->get();
        return view('admin.agentFormation.form_agents',compact('plan_formations','sexes','status','categories','corps','structures'));
    }

    public function insert(Request $request)
    {
        
 		$rules = [
            'nom_prenoms' =>'required'
        ];
 
        $validator = Validator::make($request->all(), $rules);
        $agenExist = $this->agentExist($request->matricule);
        if($validator->fails() ||  $agenExist == true)
        {
            return redirect()->back()->with('validation','Ecrvez le nom et assurez vous que l\'agents n\'existe pas déjà');
        }
        else
        {
            $avis = true;
            if($request->has('avis_commission')){
                $avis = true;
            }else{
                $avis = false;
            }
            $agent = new AgentFormation;
            $agent->matricule = $request->matricule;
            $agent->diplome_base = $request->diplome_base;
            $agent->sexe = $request->sexe;
            $agent->nom_prenoms = $request->nom_prenoms;
            $agent->status = $request->status;
            $agent->categorie = $request->categorie;
            $agent->corps = $request->corps;
            $agent->structure = $request->structure;
            $agent->avis_commission = $avis;
            if($agent->save())
	        {

                $id_ = AgentFormation::where('matricule',$request->matricule)->first()->id;
                foreach ($request->plan_formations as $key => $id) {
                    $agentLink = new AgentPlan;
                    $agentLink->id_agent = $id_;
                    $agentLink->id_plan = $id;
                    if($agentLink->save()){}else{
                        return redirect()->back()->with('error','error');
                    }
                }
                return redirect()->back()->with('success','success');	            
	        }
	        else
	        {
	            return redirect()->back()->with('error','error');
	        }
        }
        
 		
        
    }
 
    public function delete($id)
    {
        $agent = AgentFormation::where('id',$id)->get();
        if(!$agent->isEmpty())
        {
            $agentIsDeleted = AgentFormation::find($id)->delete();
            if($agentIsDeleted)
            {
                return redirect()->back()->with('success','success');
            }
            else
            {
                return redirect()->back()->with('emerror','error');
            }
        }
        else
        {
            return redirect()->back()->with('error','error');
        }
    }


    public function prepareUpdate(Request $request)
    {
        $agent = AgentFormation::where('id',$request->id)->first();
        $id_status =  DB::table('type')->select('id')->where('wording','Statut')->first();
        $status = Level::where('id_type',$id_status->id)->get();
        $id_cat =  DB::table('type')->select('id')->where('wording','Categorie')->first();
        $categories = Level::where('id_type',$id_cat->id)->get();
        $id_corps = DB::table('type')->select('id')->where('wording','Corps de la fonction publique')->first();
        $corps = Level::where('id_type',$id_corps->id)->get();
        $id_structure =  DB::table('type')->select('id')->where('wording','Structure')->first();
        $structures = Level::where('id_type',$id_structure->id)->get();
        $id_sexe =  DB::table('type')->select('id')->where('wording','Sexe')->first();
        $sexes = Level::where('id_type',$id_sexe->id)->get();

        $agentPlans_ = DB::table('plan_formations')->select('id')->whereIn('id',AgentPlan::select('id_plan')->where('id_agent',$request->id)->get())->get();
        $agentPlans = collect();
        foreach ($agentPlans_ as $key => $value) {
            $agentPlans[] = $value->id;
        }
        $plan_formations = DB::table('plan_formations')->get();
        //dd($agentPlans->search(2),$plan_formations);

        return view('admin.agentFormation.update_agents',compact('agentPlans','plan_formations','sexes','agent','status','categories','corps','structures'));
    }

    public function update(Request $request)
    {
        
        $rules = [
            'nom_prenoms' =>'required',
            'matricule' => 'required'
        ];
 
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails())
        {
            return redirect()->back()->with('validation','error');
        }
        else
        {
            
            $agent = AgentFormation::where('matricule','123456')->first();
            $agent->diplome_base = $request->diplome_base;
            $agent->sexe = $request->sexe;
            $agent->nom_prenoms = $request->nom_prenoms;
            $agent->status = $request->status;
            $agent->categorie = $request->categorie;
            $agent->corps = $request->corps;
            $agent->structure = $request->structure;
            $agent->plan_formation = $request->plan_formation;
            if($agent->save())
            {
                return redirect()->back()->with('success','success');
            }
            else
            {
                return redirect()->back()->with('error','error');
            }
        }

    } 


    

    public function export(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $agentsheet = $spreadsheet->getActiveSheet();
        $agents = AgentFormation::all();
        $datasetname = "agents";
        $agentsheet->getCellByColumnAndRow(1,1)->setValue("Matricule")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(2,1)->setValue("Nom & Prenoms")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(3,1)->setValue("Diplôme de base")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(4,1)->setValue("Sexe")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(5,1)->setValue("Status")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(6,1)->setValue("Catégorie")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(7,1)->setValue("Corps de la fonction publique")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(8,1)->setValue("Structure")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(9,1)->setValue("Plan de formation")->getStyle()->getFont()->setBold(true);

        $i = 2;
        foreach($agents as $agent)
        {
            $agentsheet->getCellByColumnAndRow(1,$i)->setValue($agent->matricule);
            $agentsheet->getCellByColumnAndRow(2,$i)->setValue($agent->nom_prenoms);
            $agentsheet->getCellByColumnAndRow(3,$i)->setValue($agent->diplome_base);
            $agentsheet->getCellByColumnAndRow(4,$i)->setValue($agent->sexe);
            $agentsheet->getCellByColumnAndRow(5,$i)->setValue($agent->status);
            $agentsheet->getCellByColumnAndRow(6,$i)->setValue($agent->categorie);
            $agentsheet->getCellByColumnAndRow(7,$i)->setValue($agent->corps);
            $agentsheet->getCellByColumnAndRow(8,$i)->setValue($agent->structure);
            $agentsheet->getCellByColumnAndRow(9,$i)->setValue($agent->plan_formation);
            $i++;
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $path = public_path();
        $path = $path."/template/$datasetname.xlsx";
        $writer->save($path);

        return redirect()->back()->with("path","template/$datasetname.xlsx");
    }


    public function buildAncienAgent(Spreadsheet $spreadsheet)
    {
        $datasetSheet = new Worksheet($spreadsheet,'Anciens agents');
        $spreadsheet->addSheet($datasetSheet, 0);
        
        $datasetSheet = $spreadsheet->getSheet(0);
        $datasetSheet->getCellByColumnAndRow(1,1)->setValue("Matricule")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(2,1)->setValue("Nom & Prenoms")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(3,1)->setValue("Diplôme de base")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(4,1)->setValue("Sexe")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(5,1)->setValue("Status")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(6,1)->setValue("Catégorie")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(7,1)->setValue("Corps de la fonction publique")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(8,1)->setValue("Structure")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(9,1)->setValue("Plan de formation")->getStyle()->getFont()->setBold(true);

        $agents = AgentFormation::all();
        $i = 2;
        foreach($agents as $agent)
        {
            $datasetSheet->getCellByColumnAndRow(1,$i)->setValue($agent->matricule);
            $datasetSheet->getCellByColumnAndRow(2,$i)->setValue($agent->nom_prenoms);
            $datasetSheet->getCellByColumnAndRow(3,$i)->setValue($agent->diplome_base);
            $datasetSheet->getCellByColumnAndRow(4,$i)->setValue($agent->sexe);
            $datasetSheet->getCellByColumnAndRow(5,$i)->setValue($agent->status);
            $datasetSheet->getCellByColumnAndRow(6,$i)->setValue($agent->categorie);
            $datasetSheet->getCellByColumnAndRow(7,$i)->setValue($agent->corps);
            $datasetSheet->getCellByColumnAndRow(8,$i)->setValue($agent->structure);
            $datasetSheet->getCellByColumnAndRow(9,$i)->setValue($agent->plan_formation);
            $i++;
        }

        
        return $spreadsheet;
    }


    public function buildStructure(Spreadsheet $spreadsheet)
    {
        $datasetSheet = new Worksheet($spreadsheet,'Structures');
        $spreadsheet->addSheet($datasetSheet, 0);
        
        $datasetSheet = $spreadsheet->getSheet(0);
        $datasetSheet->getCellByColumnAndRow(1,1)->setValue("Code")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(2,1)->setValue("Structures")->getStyle()->getFont()->setBold(true);

        $id_structure =  DB::table('type')->select('id')->where('wording','Structure')->first();
        $structures = Level::where('id_type',$id_structure->id)->get();
        $i = 2;
        foreach($structures as $structure)
        {
            $datasetSheet->getCellByColumnAndRow(1,$i)->setValue($structure->id);
            $datasetSheet->getCellByColumnAndRow(2,$i)->setValue($structure->wording);
            $i++;
        }

        
        return $spreadsheet;
    }


    public function buildCategorie(Spreadsheet $spreadsheet)
    {
        $datasetSheet = new Worksheet($spreadsheet,'Categories');
        $spreadsheet->addSheet($datasetSheet, 0);
        
        $datasetSheet = $spreadsheet->getSheet(0);
        $datasetSheet->getCellByColumnAndRow(1,1)->setValue("Code")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(2,1)->setValue("Categories")->getStyle()->getFont()->setBold(true);

        $id_cat =  DB::table('type')->select('id')->where('wording','Categorie')->first();
        $categories = Level::where('id_type',$id_cat->id)->get();
        $i = 2;
        foreach($categories as $categorie)
        {
            $datasetSheet->getCellByColumnAndRow(1,$i)->setValue($categorie->id);
            $datasetSheet->getCellByColumnAndRow(2,$i)->setValue($categorie->wording);
            $i++;
        }

        
        return $spreadsheet;
    }

    public function buildCorps(Spreadsheet $spreadsheet)
    {
        $datasetSheet = new Worksheet($spreadsheet,'Corps de la fonction publique');
        $spreadsheet->addSheet($datasetSheet, 0);
        
        $datasetSheet = $spreadsheet->getSheet(0);
        $datasetSheet->getCellByColumnAndRow(1,1)->setValue("Code")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(2,1)->setValue("Corps")->getStyle()->getFont()->setBold(true);

        $id_corps = DB::table('type')->select('id')->where('wording','Corps de la fonction publique')->first();
        $corps = Level::where('id_type',$id_corps->id)->get();
        $i = 2;
        foreach($corps as $cor)
        {
            $datasetSheet->getCellByColumnAndRow(1,$i)->setValue($cor->id);
            $datasetSheet->getCellByColumnAndRow(2,$i)->setValue($cor->wording);
            $i++;
        }

        
        return $spreadsheet;
    }

    public function buildStatuts(Spreadsheet $spreadsheet)
    {
        $datasetSheet = new Worksheet($spreadsheet,'Statuts');
        $spreadsheet->addSheet($datasetSheet, 0);
        
        $datasetSheet = $spreadsheet->getSheet(0);
        $datasetSheet->getCellByColumnAndRow(1,1)->setValue("Code")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(2,1)->setValue("Statuts")->getStyle()->getFont()->setBold(true);

        $id_status =  DB::table('type')->select('id')->where('wording','Statut')->first();
        $statuts = Level::where('id_type',$id_status->id)->get();
        $i = 2;
        foreach($statuts as $statut)
        {
            $datasetSheet->getCellByColumnAndRow(1,$i)->setValue($statut->id);
            $datasetSheet->getCellByColumnAndRow(2,$i)->setValue($statut->wording);
            $i++;
        }

        
        return $spreadsheet;
    }

    public function buildCountry(Spreadsheet $spreadsheet)
    {
        $datasetSheet = new Worksheet($spreadsheet,'Pays');
        $spreadsheet->addSheet($datasetSheet, 0);
        
        $datasetSheet = $spreadsheet->getSheet(0);
        $datasetSheet->getCellByColumnAndRow(1,1)->setValue("Code")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(2,1)->setValue("Pays")->getStyle()->getFont()->setBold(true);

        $countries =  DB::table('countries')->get();
        $i = 2;
        foreach($countries as $country)
        {
            $datasetSheet->getCellByColumnAndRow(1,$i)->setValue($country->id);
            $datasetSheet->getCellByColumnAndRow(2,$i)->setValue($country->name);
            $i++;
        }

        
        return $spreadsheet;
    }

    public function buildCity(Spreadsheet $spreadsheet)
    {
        $datasetSheet = new Worksheet($spreadsheet,'Villes');
        $spreadsheet->addSheet($datasetSheet, 0);
        
        $datasetSheet = $spreadsheet->getSheet(0);
        $datasetSheet->getCellByColumnAndRow(1,1)->setValue("Code")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(2,1)->setValue("Ville")->getStyle()->getFont()->setBold(true);

        $cities =  DB::table('cities')->get();
        $i = 2;
        foreach($cities as $citie)
        {
            $datasetSheet->getCellByColumnAndRow(1,$i)->setValue($citie->id);
            $datasetSheet->getCellByColumnAndRow(2,$i)->setValue($citie->name);
            $i++;
        }

        
        return $spreadsheet;
    }

    public function buildSexe(Spreadsheet $spreadsheet)
    {
        $datasetSheet = new Worksheet($spreadsheet,'Sexe');
        $spreadsheet->addSheet($datasetSheet, 0);
        
        $datasetSheet = $spreadsheet->getSheet(0);
        $datasetSheet->getCellByColumnAndRow(1,1)->setValue("Code")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(2,1)->setValue("Sexe")->getStyle()->getFont()->setBold(true);

        $id_sexe =  DB::table('type')->select('id')->where('wording','Sexe')->first();
        $sexes = Level::where('id_type',$id_sexe->id)->get();
        $i = 2;
        foreach($sexes as $sexe)
        {
            $datasetSheet->getCellByColumnAndRow(1,$i)->setValue($sexe->id);
            $datasetSheet->getCellByColumnAndRow(2,$i)->setValue($sexe->wording);
            $i++;
        }

        
        return $spreadsheet;
    }


    public function buildAnnee(Spreadsheet $spreadsheet)
    {
        $datasetSheet = new Worksheet($spreadsheet,'Années');
        $spreadsheet->addSheet($datasetSheet, 0);
        
        $datasetSheet = $spreadsheet->getSheet(0);
        $datasetSheet->getCellByColumnAndRow(1,1)->setValue("Code")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(2,1)->setValue("Année")->getStyle()->getFont()->setBold(true);

        
        $annees = DB::table('annee')->get();
        $i = 2;
        foreach($annees as $annee)
        {
            $datasetSheet->getCellByColumnAndRow(1,$i)->setValue($annee->id);
            $datasetSheet->getCellByColumnAndRow(2,$i)->setValue($annee->value);
            $i++;
        }

        
        return $spreadsheet;
    }

    public function genererExp(Request $request)
    {


        $spreadsheet = new Spreadsheet();
        $spreadsheet = $this->buildAncienAgent($spreadsheet);
        $spreadsheet = $this->buildCategorie($spreadsheet);
        $spreadsheet = $this->buildStructure($spreadsheet);
        $spreadsheet = $this->buildCorps($spreadsheet);
        $spreadsheet = $this->buildSexe($spreadsheet);
        $spreadsheet = $this->buildStatuts($spreadsheet);
        
        $agentsheet = $spreadsheet->getActiveSheet();
        $agentsheet->setTitle('Nouvel Agent');
        $agentsheet->getCellByColumnAndRow(1,1)->setValue("Matricule")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(2,1)->setValue("Nom & Prenoms")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(3,1)->setValue("Diplôme de base")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(4,1)->setValue("SexeCode")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(5,1)->setValue("Sexe")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(6,1)->setValue("StatusCode")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(7,1)->setValue("Status")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(8,1)->setValue("CatégorieCode")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(9,1)->setValue("Catégorie")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(10,1)->setValue("CorpsCode")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(11,1)->setValue("Corps de la fonction publique")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(12,1)->setValue("StructureCode")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(13,1)->setValue("Structure")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(14,1)->setValue("Plan de Formation")->getStyle()->getFont()->setBold(true);

        $datasetname = "agents_formation";
       
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $path = public_path();
        $path = $path."/template/$datasetname.xlsx";
        $writer->save($path);

        return redirect()->back()->with("examplaire","template/$datasetname.xlsx");
    }


    public function formImport()
    {
        $id_structure =  DB::table('type')->select('id')->where('wording','Structure')->first();
        $structures = Level::where('id_type',$id_structure->id)->get();
        return view('admin.agentFormation.formImport_agents',compact('structures'));
    }


    public function import(Request $request)
    {
        $rules = array('importfile' => 'mimes:xlsx,xls');
 
        $file = $request->file('importfile');//->getClientOriginalExtension();
        $extension = $file->getClientOriginalExtension();
        $allowed = ['xlsx','xls'];
        //dd($file->getClientOriginalExtension());
        $validator = Validator::make($request->all(), $rules);
        $linesWithError = [];
        if(!in_array($extension,$allowed))
        {
            return redirect()->back()->with('validation','error');
        }
        else
        {
            
            $spreadsheet = IOFactory::load($file);

            $numberOfSheet = $spreadsheet->getSheetCount();
            $agentsheet = $spreadsheet->getSheet(($numberOfSheet-1));

            $numberOfRow = $agentsheet->getHighestRow();
            
            // $highestColumn = $domainsheet->getHighestColumn(); // e.g 'F'
            // $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn); // e.g. 5


            //dd($numberOfRow);
            for($i = 2; $i <= $numberOfRow; $i++)
            {
                $currentMatricule = $agentsheet->getCellByColumnAndRow(1,$i)->getValue();
                if(!is_int($currentMatricule)) $currentMatricule =  $agentsheet->getCellByColumnAndRow(1,$i)->getCalculatedValue();
                $currentName = $agentsheet->getCellByColumnAndRow(2,$i)->getValue();
                $currentDiplome = $agentsheet->getCellByColumnAndRow(3,$i)->getValue();
                $sexeCode = $agentsheet->getCellByColumnAndRow(4,$i)->getValue();
                if(!is_int($sexeCode)) $sexeCode =  $agentsheet->getCellByColumnAndRow(4,$i)->getCalculatedValue();
                $statusCode =  $agentsheet->getCellByColumnAndRow(6,$i)->getValue();
                if(!is_int($statusCode)) $statusCode =  $agentsheet->getCellByColumnAndRow(6,$i)->getCalculatedValue();
                $cateCode = $agentsheet->getCellByColumnAndRow(8,$i)->getValue();
                if(!is_int($cateCode)) $cateCode =  $agentsheet->getCellByColumnAndRow(8,$i)->getCalculatedValue();
                $corpsCode = $agentsheet->getCellByColumnAndRow(10,$i)->getValue();
                if(!is_int($corpsCode)) $corpsCode =  $agentsheet->getCellByColumnAndRow(10,$i)->getCalculatedValue();
                $structureCode = $agentsheet->getCellByColumnAndRow(12,$i)->getValue();
                if(!is_int($structureCode)) $structureCode =  $agentsheet->getCellByColumnAndRow(12,$i)->getCalculatedValue();
                $plan = $agentsheet->getCellByColumnAndRow(14,$i)->getValue();
                
                //dd($statusCode,$cateCode,$corpsCode,$structureCode,$sexeCode);
                if($plan instanceof RichText)
                {
                    $plan = $plan->getPlainText();
                }
                else
                {
                    $plan = (string)$plan;
                }
                
                
                /* Statut */ 
                if ($statusCode == null) {
                    $NotCorrect = "Le statusCode  fourni à la ligne ".$i." n'est pas correct.<br> Veillez retirer du fichier les lignes précédentes car déjà <em>enrégistrées</em> ou vous courrez le risque d'avoir des doublons dans la base.";
                    return redirect()->back()->with('nomenclatureError',$NotCorrect);
                }
                //dd($statusCode,$sexeCode,$cateCode,$corpsCode,$structureCode);
                if($statusCode instanceof RichText)
                {
                    $statusCode = $statusCode->getPlainText();
                }
                else
                {
                    $statusCode = (string)$statusCode;
                }
                $status = Level::where('id',$statusCode)->first();
                if($status == null){
                    $NotCorrect = "Le status  fourni à la ligne ".$i." n'est pas dans la base. <br> Veillez retirer du fichier les lignes précédentes car déjà <em>enrégistrées</em> ou vous courrez le risque d'avoir des doublons dans la base.";
                    return redirect()->back()->with('nomenclatureError',$NotCorrect);
                }

                /* Categorie */ 

                if ($cateCode == null) {
                    $NotCorrect = "Le cateCode  fourni à la ligne ".$i." n'est pas correct. <br> Veillez retirer du fichier les lignes précédentes car déjà <em>enrégistrées</em> ou vous courrez le risque d'avoir des doublons dans la base.";
                    return redirect()->back()->with('nomenclatureError',$NotCorrect);
                }
                if($cateCode instanceof RichText)
                {
                    $cateCode = $cateCode->getPlainText();
                }
                else
                {
                    $cateCode = (string)$cateCode;
                }
                $cat = Level::where('id',$cateCode)->first();
                if($cat == null){
                    $NotCorrect = "La categorie  fournie à la ligne ".$i." n'est pas dans la base.  <br> Veillez retirer du fichier les lignes précédentes car déjà <em>enrégistrées</em> ou vous courrez le risque d'avoir des doublons dans la base.";
                    return redirect()->back()->with('nomenclatureError',$NotCorrect);
                }

                /* Corps */
                if ($corpsCode == null) {
                    $NotCorrect = "Le corpsCode  fourni à la ligne ".$i." n'est pas correct. <br> Veillez retirer du fichier les lignes précédentes car déjà <em>enrégistrées</em> ou vous courrez le risque d'avoir des doublons dans la base.";
                    return redirect()->back()->with('nomenclatureError',$NotCorrect);
                }
                if($corpsCode instanceof RichText)
                {
                    $corpsCode = $corpsCode->getPlainText();
                }
                else
                {
                    $corpsCode = (string)$corpsCode;
                }
                // Formating
                $corps = Level::where('id',$corpsCode)->first();
                if($corps == null){
                    $NotCorrect = "Le corps  fourni à la ligne ".$i." n'est pas dans la base.  <br> Veillez retirer du fichier les lignes précédentes car déjà <em>enrégistrées</em> ou vous courrez le risque d'avoir des doublons dans la base.";
                    return redirect()->back()->with('nomenclatureError',$NotCorrect);
                }

                if ($structureCode == null) {
                    $NotCorrect = "Le strucureCode  fourni à la ligne ".$i." n'est pas correct. <br> Veillez retirer du fichier les lignes précédentes car déjà <em>enrégistrées</em> ou vous courrez le risque d'avoir des doublons dans la base.";
                    return redirect()->back()->with('nomenclatureError',$NotCorrect);
                }
                if($structureCode instanceof RichText)
                {
                    $structureCode = $structureCode->getPlainText();
                }
                else
                {
                    $structureCode = (string)$structureCode;
                }
                // Formating
                $struct = Level::where('id',$structureCode)->first();
                if($struct == null){
                    $NotCorrect = "La structure  fournie à la ligne ".$i." n'est pas dans la base.  <br> Veillez retirer du fichier les lignes précédentes car déjà <em>enrégistrées</em> ou vous courrez le risque d'avoir des doublons dans la base.";
                    return redirect()->back()->with('nomenclatureError',$NotCorrect);
                }


                if($currentDiplome instanceof RichText)
                {
                    $currentDiplome = $currentDiplome->getPlainText();
                }
                else
                {
                    $currentDiplome = (string)$currentDiplome;
                }


                //Formating   
                if ($sexeCode == null) {
                    $NotCorrect = "Le sexeCode  fourni à la ligne ".$i." n'est pas correct. <br> Veillez retirer du fichier les lignes précédentes car déjà <em>enrégistrées</em> ou vous courrez le risque d'avoir des doublons dans la base.";
                    return redirect()->back()->with('nomenclatureError',$NotCorrect);
                }
                $sexe = Level::where('id',$sexeCode)->first();
                if($sexe == null){
                    $NotCorrect = "Le sexe  fourni à la ligne ".$i." n'est pas dans la base.  <br> Veillez retirer du fichier les lignes précédentes car déjà <em>enrégistrées</em> ou vous courrez le risque d'avoir des doublons dans la base.";
                    return redirect()->back()->with('nomenclatureError',$NotCorrect);
                }
                

                if($currentName instanceof RichText)
                {
                    $currentName = $currentName->getPlainText();
                }
                else
                {
                    $currentName = (string)$currentName;
                }
                //Formating
                if($currentName == "null" || $currentName == null){
                    $NotCorrect = "Le nom et prenom de l'agent à la ligne  ".$i." n'est pas correct. <br> Veillez retirer du fichier les lignes précédentes car déjà <em>enrégistrées</em> ou vous courrez le risque d'avoir des doublons dans la base.";
                    return redirect()->back()->with('nomenclatureError',$NotCorrect);
                }


                if($currentMatricule instanceof RichText)
                {
                    $currentMatricule = $currentMatricule->getPlainText();
                }
                else
                {
                    $currentMatricule = (string)$currentMatricule;
                }
                if($this->agentExist($currentMatricule))
                {   
                    $errormsg = "l'Agent existe deja.";
                    $linesWithError[] = $i;
                }
                else
                {   
                    $agent = new AgentFormation;
                    $agent->matricule = $currentMatricule;
                    $agent->sexe = $sexe->wording;
                    $agent->nom_prenoms = $currentName;
                    $agent->diplome_base = $currentDiplome;
                    $agent->categorie = $cat->wording;
                    $agent->status = $status->wording;
                    $agent->corps = $corps->wording;
                    $agent->structure = $struct->wording;
                    $agent->plan_formation = $plan;
                    if($agent->save())
                    {
                        continue;
                    }
                    else
                    {
                        $errormsg = "l'Erreur s'est produite lors de la mise des données dans la base.  <br> Veillez retirer du fichier les lignes précédentes car déjà <em>enrégistrées</em> ou vous courrez le risque d'avoir des doublons dans la base.";
                        $linesWithError[] = $i;
                    }
                }
            }

            if($linesWithError == null || (count($linesWithError)==0))
            {
                return redirect()->back()->with('success','success');
            }
            else
            {
                $errorSpreadSheet = $this->copyRowsFromArray($linesWithError, $spreadsheet);

                $writer = new Xlsx($errorSpreadSheet);
                $path = public_path();
                $path = $path."/template/unsaved.xlsx";
                $writer->save($path);

                return redirect()->back()->with("path","template/unsaved.xlsx") 
                                        ->with('warning',$errormsg);
            }

        }

    }

    public function agentExist($mat)
    {
        
        $matricule = trim(strtolower($mat));
        $agent = AgentFormation::where('matricule',$matricule)->first();
        if($agent == null)
        {
            return false;
        }

        return true;
    }

    public function copyRowsFromArray($rows, Spreadsheet $oldSpreadSheet)
    {
        $spreadsheet = new Spreadsheet();
        $numberOfSheet = $oldSpreadSheet->getSheetCount();
        $errorSheet = $spreadsheet->getActiveSheet();
        
        $oldDataSheet = $oldSpreadSheet->getSheet(($numberOfSheet-1));
        $highestColumn = $oldDataSheet->getHighestDataColumn(); // e.g 'F'
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn); // e.g. 5

        $numberOfRows = count($rows);
        $i = 0;
        for($row = 1; $row <= $numberOfRows; $row++)
        {
            for($j = 1; $j <= $highestColumnIndex; $j++)
            {
                $oldCellValue = $oldDataSheet->getCellByColumnAndRow($j,$rows[$i])->getValue();

                if($oldCellValue instanceof  RichText)
                {
                    $oldCellValue = $oldCellValue->getPlainText();
                }
                if(!is_string($oldCellValue) && $oldCellValue != null)
                {
                     $oldCellValue = (int)$oldCellValue;
                     if($oldCellValue == 0)
                     {
                        $oldCellValue = "";
                     }
                }
                $errorSheet->getCellByColumnAndRow($j,$row)->setValue($oldCellValue);
            }
            $i++;
        }
        
        return $spreadsheet;
    }


    public function oneLine()
    {

        $spreadsheet = new Spreadsheet();
        $agentsheet = $spreadsheet->getActiveSheet();
        $agents = AgentFormation::all();
        $datasetname = "Donnees";
        $agentsheet->getCellByColumnAndRow(1,1)->setValue("Matricule")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(2,1)->setValue("Nom & Prenoms")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(3,1)->setValue("Diplôme de base")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(4,1)->setValue("Sexe")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(5,1)->setValue("Status")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(6,1)->setValue("Catégorie")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(7,1)->setValue("Corps de la fonction publique")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(8,1)->setValue("Structure")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(9,1)->setValue("Plan de formation")->getStyle()->getFont()->setBold(true);

        $agentsheet->getCellByColumnAndRow(10,1)->setValue("Date de signature de mise en stage")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(11,1)->setValue("Date de démarrage du stage")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(12,1)->setValue("Numéro de référence MS")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(13,1)->setValue("Est Boursier ?(0 ou 1)")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(14,1)->setValue("Nature de la bourse")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(15,1)->setValue("Durée du stage")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(16,1)->setValue("Année du stage")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(17,1)->setValue("Niveau")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(18,1)->setValue("Filière")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(19,1)->setValue("Spécialité/Option")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(20,1)->setValue("Pays du stage")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(21,1)->setValue("Ville du stage")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(22,1)->setValue("Ecole du stage")->getStyle()->getFont()->setBold(true);

        $agentsheet->getCellByColumnAndRow(23,1)->setValue("Numéro de référence RS")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(24,1)->setValue("Date signature de retour en stage")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(25,1)->setValue("Date de fin de formation")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(26,1)->setValue("Date de reprise de service")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(27,1)->setValue("Catégorie RS")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(28,1)->setValue("Année RS")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(29,1)->setValue("Incidence BN")->getStyle()->getFont()->setBold(true);
        $agentsheet->getCellByColumnAndRow(30,1)->setValue("Structure RS")->getStyle()->getFont()->setBold(true);
        $i = 2;
        foreach($agents as $agent)
        {
            $mise_m = MiseEnStage::where('id_agent',$agent->id)->first();
            $mise_r = RetourDeStage::where('id_agent',$agent->id)->first();
            $agentsheet->getCellByColumnAndRow(1,$i)->setValue($agent->matricule);
            $agentsheet->getCellByColumnAndRow(2,$i)->setValue($agent->nom_prenoms);
            $agentsheet->getCellByColumnAndRow(3,$i)->setValue($agent->diplome_base);
            $agentsheet->getCellByColumnAndRow(4,$i)->setValue($agent->sexe);
            $agentsheet->getCellByColumnAndRow(5,$i)->setValue($agent->status);
            $agentsheet->getCellByColumnAndRow(6,$i)->setValue($agent->categorie);
            $agentsheet->getCellByColumnAndRow(7,$i)->setValue($agent->corps);
            $agentsheet->getCellByColumnAndRow(8,$i)->setValue($agent->structure);
            $agentsheet->getCellByColumnAndRow(9,$i)->setValue($agent->plan_formation);
            $d1 = '';
            if($mise_m != null){
                if ($mise_m->date_signature != null && $mise_m->date_signature!= 'null' && $mise_m->date_signature != "") {
                    $d1 = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($mise_m->date_signature)->format('j/m/Y');
                }else{
                    $d1 = $mise_m->date_signature;
                }
                $agentsheet->getCellByColumnAndRow(10,$i)->setValue($d1);
                if ($mise_m->date_demarrage_stage != null && $mise_m->date_demarrage_stage!= 'null' && $mise_m->date_demarrage_stage != "") {
                    $d1 = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($mise_m->date_demarrage_stage)->format('j/m/Y');
                }else{
                    $d1 = $mise_m->date_demarrage_stage;
                }
                $agentsheet->getCellByColumnAndRow(11,$i)->setValue($d1);
                $agentsheet->getCellByColumnAndRow(12,$i)->setValue($mise_m->numero_decision_ms);
                $agentsheet->getCellByColumnAndRow(13,$i)->setValue($mise_m->isBoursier);
                $agentsheet->getCellByColumnAndRow(14,$i)->setValue($mise_m->nature_bourse);
                $agentsheet->getCellByColumnAndRow(15,$i)->setValue($mise_m->duree);
                $agentsheet->getCellByColumnAndRow(16,$i)->setValue($mise_m->annee_stage);
                $agentsheet->getCellByColumnAndRow(17,$i)->setValue($mise_m->niveau);
                $agentsheet->getCellByColumnAndRow(18,$i)->setValue($mise_m->filiere);
                $agentsheet->getCellByColumnAndRow(19,$i)->setValue($mise_m->spec_option);
                $agentsheet->getCellByColumnAndRow(20,$i)->setValue($mise_m->country->name);
                $agentsheet->getCellByColumnAndRow(21,$i)->setValue($mise_m->city->name);
                $agentsheet->getCellByColumnAndRow(22,$i)->setValue($mise_m->ecole_stage);
                
            }
            if($mise_r != null){
                $agentsheet->getCellByColumnAndRow(23,$i)->setValue($mise_r->numero_decision_rs);
                if ($mise_r->date_signature != null && $mise_r->date_signature!= 'null' && $mise_r->date_signature != "") {
                    $d1 = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($mise_r->date_signature)->format('j/m/Y');
                }else{
                    $d1 = $mise_r->date_signature;
                }
                $agentsheet->getCellByColumnAndRow(24,$i)->setValue($d1);
                if ($mise_r->date_fin_formation != null && $mise_r->date_fin_formation!= 'null' && $mise_r->date_fin_formation != "") {
                    $d1 = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($mise_r->date_fin_formation)->format('j/m/Y');
                }else{
                    $d1 = $mise_r->date_fin_formation;
                }
                $agentsheet->getCellByColumnAndRow(25,$i)->setValue($d1);
                if ($mise_r->date_reprise_service != null && $mise_r->date_reprise_service!= 'null' && $mise_r->date_reprise_service != "") {
                    $d1 = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($mise_r->date_reprise_service)->format('j/m/Y');
                }else{
                    $d1 = $mise_r->date_reprise_service;
                }
                $agentsheet->getCellByColumnAndRow(26,$i)->setValue($d1);
                $agentsheet->getCellByColumnAndRow(27,$i)->setValue($mise_r->categorie_rs);
                $agentsheet->getCellByColumnAndRow(28,$i)->setValue($mise_r->annee_rs);
                $agentsheet->getCellByColumnAndRow(29,$i)->setValue($mise_r->incidence_bn);
                $agentsheet->getCellByColumnAndRow(30,$i)->setValue($mise_r->structure_rs);
            }
            
            $i++;
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $path = public_path();
        $path = $path."/template/$datasetname.xlsx";
        $writer->save($path);

        return redirect()->back()->with("data","template/$datasetname.xlsx");
    }
    
}
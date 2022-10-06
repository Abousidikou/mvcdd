<?php

namespace App\Http\Controllers\MiseEnStage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AgentFormation\AgentFormationController;
use App\Models\AgentFormation;
use App\Models\Level;
use App\Models\{Country,State,City};

use App\Models\MiseEnStage;
use App\Models\AgentPlan;

class MiseEnStageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        $m_stages = MiseEnStage::all();
        foreach ($m_stages as $mise) {
            $c = Country::where('id',$mise->pays_stage_id)->first();
            $r = State::where('id',$mise->region_stage_id)->first();
            $v = City::where('id',$mise->ville_stage_id)->first();
            $mise->pays_stage = ( $c != null) ? $c->name : "Not specified" ;
            $mise->region_stage = ( $r != null) ? $r->name : "Not specified" ;
            $mise->ville_stage = ( $v != null) ? $v->name : "Not specified" ;
        }
        return view('admin.mise_stage.data',compact('m_stages'));
    }

    public function form_insert()
    {
        $agents = AgentFormation::where('avis_commission',1)->get();
        $countries =  Country::all();
        /* $agentPlans_ = DB::table('plan_formations')->select('id')->whereIn('id',AgentPlan::select('id_plan')->where('id_agent',$request->id)->get())->get();
        $agentPlans = collect();
        foreach ($agentPlans_ as $key => $value) {
            $agentPlans[] = $value->id;
        }
        $plan_formations = DB::table('plan_formations')->get(); */
        return view('admin.mise_stage.form_insert',compact('agents','countries'));
    }

    public function insert(Request $request)
    {
        $rules = [
            'id_agent' =>'required'
        ];
        $isBoursier = true;
        if($request->has('isBoursier')){
            $isBoursier = true;
        }else{
            $isBoursier = false;
        }
        
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails())
        {
            return redirect()->back()->with('validation','error');
        }
        else
        {   
            $isagent = AgentFormation::where('id',$request->id_agent)->first();
            if($isagent == null)
            {
                return redirect()->back()->with('agents_not_exist','error');
            }
            $mise_s = new MiseEnStage;
            $mise_s->id_agent = $request->id_agent;
            $mise_s->numero_decision_ms = $request->numero_decision_ms;
            $mise_s->date_signature = $request->date_signature;
            $mise_s->isBoursier = $isBoursier;
            $mise_s->nature_bourse = $request->nature_bourse;
            $mise_s->date_demarrage_stage = $request->date_demarrage_stage;
            $mise_s->niveau = $request->niveau;
            $mise_s->annee_stage = $request->annee_stage;
            $mise_s->filiere = $request->filiere;
            $mise_s->spec_option = $request->spec_option;
            $mise_s->ecole_stage = $request->ecole_stage;
            $mise_s->duree = $request->duree;
            $mise_s->pays_stage_id = $request->pays_stage;
            $mise_s->region_stage_id = $request->region_stage;
            $mise_s->ville_stage_id = $request->ville_stage;

            if($mise_s->save())
            {
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
        $m_s = MiseEnStage::where('id',$id)->get();
        if(!$m_s->isEmpty())
        {
            $m_sIsDeleted = MiseEnStage::find($id)->delete();
            if($m_sIsDeleted)
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
        $mise_stage = MiseEnStage::where('id',$request->id)->first();
        $agents = AgentFormation::all();
        $countries =  Country::all();
        $agentPlans = DB::table('plan_formations')->whereIn('id',AgentPlan::select('id_plan')->where('id_agent',$request->id)->get())->get();
        /* $agentPlans = collect();
        foreach ($agentPlans_ as $key => $value) {
            $agentPlans[] = $value->id;
        }
        $plan_formations = DB::table('plan_formations')->get(); */
        return view('admin.mise_stage.form_update',compact('agentPlans','agents','mise_stage','countries'));
    }

    public function update(Request $request)
    {
        $rules = [
            'id_agent' =>'required'
        ];
 
        $valid = MiseEnStage::where('id',$request->id)->first();
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails() || $valid == null)
        {
            return redirect()->back()->with('validation','error');
        }
        else
        {
            $mise_s =  MiseEnStage::where('id',$request->id)->first();
            $isBoursier = true;
            if($request->has('isBoursier')){
                $isBoursier = true;
            }else{
                $isBoursier = false;
            }
            $mise_s->id_agent = $request->id_agent;
            $mise_s->numero_decision_ms = $request->numero_decision_ms;
            $mise_s->date_signature = $request->date_signature;
            $mise_s->isBoursier = $isBoursier;
            $mise_s->nature_bourse = $request->nature_bourse;
            $mise_s->date_demarrage_stage = $request->date_demarrage_stage;
            $mise_s->niveau = $request->niveau;
            $mise_s->annee_stage = $request->annee_stage;
            $mise_s->filiere = $request->filiere;
            $mise_s->spec_option = $request->spec_option;
            $mise_s->ecole_stage = $request->ecole_stage;
            $mise_s->duree = $request->duree;
            $mise_s->pays_stage_id = $request->pays_stage;
            $mise_s->region_stage_id = $request->region_stage;
            $mise_s->ville_stage_id = $request->ville_stage;
            if($mise_s->save())
            {
                return redirect()->back()->with('success','success');
            }
            else
            {
                return redirect()->back()->with('error','error');
            }
        }

    } 

    public function form_import()
    {
        $id_structure =  DB::table('type')->select('id')->where('wording','Structure')->first();
        $structures = Level::where('id_type',$id_structure->id)->get();
        $countries =  Country::all();
        return view('admin.mise_stage.form_import',compact('structures','countries'));
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
        $errormsg = "";
        if(!in_array($extension,$allowed))
        {
            return redirect()->back()->with('validation','error');
        }
        else
        {
            $spreadsheet = IOFactory::load($file);

            $numberOfSheet = $spreadsheet->getSheetCount();
            $mise_stage = $spreadsheet->getSheet(($numberOfSheet-1));

            $numberOfRow = $mise_stage->getHighestRow();
            
            // $highestColumn = $domainsheet->getHighestColumn(); // e.g 'F'
            // $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn); // e.g. 5

            for($i = 2; $i <= $numberOfRow; $i++)
            {
                
                $currentMatricule = $mise_stage->getCellByColumnAndRow(1,$i)->getValue();
                if(!is_int($currentMatricule)) $currentMatricule =  $mise_stage->getCellByColumnAndRow(1,$i)->getCalculatedValue();
                $date_signature = $mise_stage->getCellByColumnAndRow(3,$i)->getValue();
                $date_demarrage_stage = $mise_stage->getCellByColumnAndRow(4,$i)->getValue();
                $numero_decision_ms = $mise_stage->getCellByColumnAndRow(5,$i)->getValue();
                $isBoursier = $mise_stage->getCellByColumnAndRow(6,$i)->getValue();
                if(!is_int($isBoursier)) $isBoursier =  $mise_stage->getCellByColumnAndRow(6,$i)->getCalculatedValue();
                $nature_bourse = $mise_stage->getCellByColumnAndRow(7,$i)->getValue();
                $duree = $mise_stage->getCellByColumnAndRow(8,$i)->getValue();
                $anneeCode = $mise_stage->getCellByColumnAndRow(9,$i)->getValue();
                if(!is_int($anneeCode)) $anneeCode =  $mise_stage->getCellByColumnAndRow(9,$i)->getCalculatedValue();
                //if(!is_int($sexeCode)) $sexeCode =  $agentsheet->getCellByColumnAndRow(4,$i)->getCalculatedValue();
                $niveau = $mise_stage->getCellByColumnAndRow(11,$i)->getValue();
                $filiere = $mise_stage->getCellByColumnAndRow(12,$i)->getValue();
                $spec_option = $mise_stage->getCellByColumnAndRow(13,$i)->getValue();
                $cityCode = $mise_stage->getCellByColumnAndRow(15,$i)->getValue();
                $ecole_stage = $mise_stage->getCellByColumnAndRow(17,$i)->getValue();

                //if($i == 3) dd($currentMatricule);
                if($currentMatricule == null) break;
                //dd($isBoursier);
                if ($isBoursier != null) {
                    if($isBoursier instanceof RichText)
                    {
                        $isBoursier = $isBoursier->getPlainText();
                    }
                    else
                    {
                        $isBoursier = (string)$isBoursier;
                    }
                    //Test
                    if ($isBoursier != "1" && $isBoursier != "0") {
                        $NotCorrect = "IsBouriser au niveau de la ligne ".$i." n'est pas correct.  <br> Veillez retirer du fichier les lignes précédentes car déjà <em>enrégistrées</em> ou vous courrez le risque d'avoir des doublons dans la base.";
                        return redirect()->back()->with('nomenclatureError',$NotCorrect);
                    } 
                } else {
                    $isBoursier = 0;
                }
                

                if($spec_option instanceof RichText)
                {
                    $spec_option = $spec_option->getPlainText();
                }
                else
                {
                    $spec_option = (string)$spec_option;
                }

                
                if ($cityCode == null) {
                    $NotCorrect = "CityCode au niveau de la ligne ".$i." n'est pas correct.  <br> Veillez retirer du fichier les lignes précédentes car déjà <em>enrégistrées</em> ou vous courrez le risque d'avoir des doublons dans la base.";
                    return redirect()->back()->with('nomenclatureError',$NotCorrect);
                }
                
                if($cityCode instanceof RichText)
                {
                    $cityCode = $cityCode->getPlainText();
                }
                else
                {
                    $cityCode = (int)$cityCode;
                }

                
                // appel a model 
                $city = City::where('id',$cityCode)->first();
                if ($city == null) {
                    $NotCorrect = "Le code de ville fourni à la ligne ".$i." n'est pas dans la base.  <br> Veillez retirer du fichier les lignes précédentes car déjà <em>enrégistrées</em> ou vous courrez le risque d'avoir des doublons dans la base.";
                    return redirect()->back()->with('nomenclatureError',$NotCorrect);
                }
                //Appel a state
                $state = State::where('id',$city->state_id)->first();
                //Appel a country
                $country = Country::where('id',$state->country_id)->first();

                if($filiere instanceof RichText)
                {
                    $filiere = $filiere->getPlainText();
                }
                else
                {
                    $filiere = (string)$filiere;
                }

                if($niveau instanceof RichText)
                {
                    $niveau = $niveau->getPlainText();
                }
                else
                {
                    $niveau = (string)$niveau;
                }

                
                if ($anneeCode == null) {
                    $NotCorrect = "AnneeCode au niveau de la ligne ".$i." n'est pas correct.  <br> Veillez retirer du fichier les lignes précédentes car déjà <em>enrégistrées</em> ou vous courrez le risque d'avoir des doublons dans la base.";
                    return redirect()->back()->with('nomenclatureError',$NotCorrect);
                }
                if($anneeCode instanceof RichText)
                {
                    $anneeCode = $anneeCode->getPlainText();
                }
                else
                {
                    $anneeCode = (string)$anneeCode;
                }
                //dd($anneeCode);
                // appel a model 
                $annee = DB::table('annee')->where('id',$anneeCode)->first();
                if ($annee == null) {
                    $NotCorrect = "Le code de l'année fourni à la ligne ".$i." n'est pas dans la base.  <br> Veillez retirer du fichier les lignes précédentes car déjà <em>enrégistrées</em> ou vous courrez le risque d'avoir des doublons dans la base.";
                    return redirect()->back()->with('nomenclatureError',$NotCorrect);
                }

                if($duree instanceof RichText)
                {
                    $duree = $duree->getPlainText();
                }
                else
                {
                    $duree = (string)$duree;
                }

                if($nature_bourse instanceof RichText)
                {
                    $nature_bourse = $nature_bourse->getPlainText();
                }
                else
                {
                    $nature_bourse = (string)$nature_bourse;
                }

                if($numero_decision_ms instanceof RichText)
                {
                    $numero_decision_ms = $numero_decision_ms->getPlainText();
                }
                else
                {
                    $numero_decision_ms = (string)$numero_decision_ms;
                }

                

                if($date_demarrage_stage instanceof RichText)
                {
                    $date_demarrage_stage = $date_demarrage_stage->getPlainText();
                }
                else
                {
                    $date_demarrage_stage = (string)$date_demarrage_stage;
                }
                //$date_demarrage_stage = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date_demarrage_stage);

                if($date_signature instanceof RichText)
                {
                    $date_signature = $date_signature->getPlainText();
                }
                else
                {
                    $date_signature = (string)$date_signature;
                }
                //$date_signature = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date_signature);
                
                if($currentMatricule instanceof RichText)
                {
                    $currentMatricule = $currentMatricule->getPlainText();
                }
                else
                {
                    $currentMatricule = (string)$currentMatricule;
                }
                
                if(!$this->agentExist($currentMatricule))
                {
                    
                    $errormsg = "l'Agent n'existe pas pour être mise en stage.";
                    $linesWithError[] = $i;
                }
                else
                {
                    
                    $id_agent = AgentFormation::where('matricule',$currentMatricule)->first();
                    $mise_s = new MiseEnStage;
                    $mise_s->id_agent = $id_agent->id;
                    $mise_s->numero_decision_ms = $numero_decision_ms;
                    $mise_s->date_signature = $date_signature;
                    $mise_s->isBoursier = $isBoursier;
                    $mise_s->nature_bourse = $nature_bourse;
                    $mise_s->date_demarrage_stage = $date_demarrage_stage;
                    $mise_s->niveau = $niveau;
                    $mise_s->filiere = $filiere;
                    $mise_s->spec_option = $spec_option;
                    $mise_s->ecole_stage = $ecole_stage;
                    $mise_s->duree = $duree;    
                    $mise_s->annee_stage = $annee->value;
                    $mise_s->pays_stage_id = $country->id;
                    $mise_s->region_stage_id = $state->id;
                    $mise_s->ville_stage_id = $city->id;

                    if($mise_s->save())
                    {
                        continue;
                    }
                    else
                    {
                        $errormsg = "l'Erreur s'est produite lors de la mise des données dans la base";
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

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $mise_stage = $spreadsheet->getActiveSheet();
        $mise_m = MiseEnStage::all();
        $datasetname = "mise_en_stage";
        $mise_stage->getCellByColumnAndRow(1,1)->setValue("Matricule")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(2,1)->setValue("Nom & Prenoms")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(3,1)->setValue("Date de signature de mise en stage")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(4,1)->setValue("Date de démarrage du stage")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(5,1)->setValue("Numéro de référence")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(6,1)->setValue("Est Boursier ?(0 ou 1)")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(7,1)->setValue("Nature de la bourse")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(8,1)->setValue("Durée du stage")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(9,1)->setValue("Année du stage")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(10,1)->setValue("Niveau")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(11,1)->setValue("Filière")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(12,1)->setValue("Spécialité/Option")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(13,1)->setValue("Pays du stage")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(14,1)->setValue("Ville du stage")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(15,1)->setValue("Ecole du stage")->getStyle()->getFont()->setBold(true);

        $i = 2;
        foreach($mise_m as $mise)
        {
            $mise_stage->getCellByColumnAndRow(1,$i)->setValue($mise->agent->matricule);
            $mise_stage->getCellByColumnAndRow(2,$i)->setValue($mise->agent->nom_prenoms);
            $d1 = '';
            if ($mise->date_signature != null && $mise->date_signature!= 'null' && $mise->date_signature != "") {
                $d1 = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($mise->date_signature)->format('j/m/Y');
            }else{
                $d1 = $mise->date_signature;
            }
            $mise_stage->getCellByColumnAndRow(3,$i)->setValue($d1);
            if ($mise->date_demarrage_stage != null && $mise->date_demarrage_stage!= 'null' && $mise->date_demarrage_stage != "") {
                $d1 = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($mise->date_demarrage_stage)->format('j/m/Y');
            }else{
                $d1 = $mise->date_demarrage_stage;
            }
            $mise_stage->getCellByColumnAndRow(4,$i)->setValue($d1);
            $mise_stage->getCellByColumnAndRow(5,$i)->setValue($mise->numero_decision_ms);
            $mise_stage->getCellByColumnAndRow(6,$i)->setValue($mise->isBoursier);
            $mise_stage->getCellByColumnAndRow(7,$i)->setValue($mise->nature_bourse);
            $mise_stage->getCellByColumnAndRow(8,$i)->setValue($mise->duree);
            $mise_stage->getCellByColumnAndRow(9,$i)->setValue($mise->annee_stage);
            $mise_stage->getCellByColumnAndRow(10,$i)->setValue($mise->niveau);
            $mise_stage->getCellByColumnAndRow(11,$i)->setValue($mise->filiere);
            $mise_stage->getCellByColumnAndRow(12,$i)->setValue($mise->spec_option);
            $mise_stage->getCellByColumnAndRow(13,$i)->setValue($mise->country->name);
            $mise_stage->getCellByColumnAndRow(14,$i)->setValue($mise->city->name);
            $mise_stage->getCellByColumnAndRow(15,$i)->setValue($mise->ecole_stage);
            $i++;
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $path = public_path();
        $path = $path."/template/$datasetname.xlsx";
        $writer->save($path);

        return redirect()->back()->with("path","template/$datasetname.xlsx");
    }

    public function buildoldMise(Spreadsheet $spreadsheet){
        $datasetSheet = new Worksheet($spreadsheet,'Liste Mises en Stage');
        $spreadsheet->addSheet($datasetSheet, 0);
        
        $datasetSheet = $spreadsheet->getSheet(0);


        $datasetSheet->getCellByColumnAndRow(1,1)->setValue("Matricule")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(2,1)->setValue("Nom & Prenoms")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(3,1)->setValue("Date de signature de mise en stage")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(4,1)->setValue("Date de démarrage du stage")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(5,1)->setValue("Numéro de référence")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(6,1)->setValue("Est Boursier ?[0 ou 1]")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(7,1)->setValue("Nature de la bourse")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(8,1)->setValue("Durée du stage")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(9,1)->setValue("Année du stage")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(10,1)->setValue("Niveau")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(11,1)->setValue("Filière")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(12,1)->setValue("Spécialité/Option")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(13,1)->setValue("Pays du stage")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(14,1)->setValue("Ville du stage")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(15,1)->setValue("Ecole du stage")->getStyle()->getFont()->setBold(true);

        $mise_m = MiseEnStage::all();
        $i = 2;
        foreach($mise_m as $mise)
        {
            $datasetSheet->getCellByColumnAndRow(1,$i)->setValue($mise->agent->matricule);
            $datasetSheet->getCellByColumnAndRow(2,$i)->setValue($mise->agent->nom_prenoms);
            $datasetSheet->getCellByColumnAndRow(3,$i)->setValue($mise->date_signature);
            $datasetSheet->getCellByColumnAndRow(4,$i)->setValue($mise->date_demarrage_stage);
            $datasetSheet->getCellByColumnAndRow(5,$i)->setValue($mise->numero_decision_ms);
            $datasetSheet->getCellByColumnAndRow(6,$i)->setValue($mise->isBoursier);
            $datasetSheet->getCellByColumnAndRow(7,$i)->setValue($mise->nature_bourse);
            $datasetSheet->getCellByColumnAndRow(8,$i)->setValue($mise->duree);
            $datasetSheet->getCellByColumnAndRow(9,$i)->setValue($mise->annee_stage);
            $datasetSheet->getCellByColumnAndRow(10,$i)->setValue($mise->niveau);
            $datasetSheet->getCellByColumnAndRow(11,$i)->setValue($mise->filiere);
            $datasetSheet->getCellByColumnAndRow(12,$i)->setValue($mise->spec_option);
            $datasetSheet->getCellByColumnAndRow(13,$i)->setValue($mise->country->name);
            $datasetSheet->getCellByColumnAndRow(14,$i)->setValue($mise->city->name);
            $datasetSheet->getCellByColumnAndRow(15,$i)->setValue($mise->ecole_stage);
            $i++;
        }


        return $spreadsheet;
    }

    public function genererExp()
    {
        $build = new AgentFormationController();
        $spreadsheet = new Spreadsheet();
        $spreadsheet = $this->buildoldMise($spreadsheet);
        $spreadsheet = $build->buildAncienAgent($spreadsheet);
        $spreadsheet = $build->buildAnnee($spreadsheet);
        $spreadsheet = $build->buildCountry($spreadsheet);
        $spreadsheet = $build->buildCity($spreadsheet);
        $spreadsheet = $build->buildStructure($spreadsheet);
        $spreadsheet = $build->buildCorps($spreadsheet);
        $mise_stage = $spreadsheet->getActiveSheet();
        $mise_stage->setTitle('Mise en stage');
        $mise_stage->getCellByColumnAndRow(1,1)->setValue("Matricule")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(2,1)->setValue("Nom & Prenoms")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(3,1)->setValue("Date de signature de mise en stage")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(4,1)->setValue("Date de démarrage du stage")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(5,1)->setValue("Numéro de référence")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(6,1)->setValue("Est Boursier ?(0 ou 1)")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(7,1)->setValue("Nature de la bourse")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(8,1)->setValue("Durée du stage")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(9,1)->setValue("Année Code")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(10,1)->setValue("Année du stage")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(11,1)->setValue("Niveau")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(12,1)->setValue("Filière")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(13,1)->setValue("Spécialité/Option")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(14,1)->setValue("Pays Code")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(14,1)->setValue("Pays du stage")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(15,1)->setValue("Ville Code")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(16,1)->setValue("Ville du stage")->getStyle()->getFont()->setBold(true);
        $mise_stage->getCellByColumnAndRow(17,1)->setValue("Ecole du stage")->getStyle()->getFont()->setBold(true);

       
        $datasetname = "mise_en_stages";
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $path = public_path();
        $path = $path."/template/$datasetname.xlsx";
        $writer->save($path);

        return redirect()->back()->with("examplaire","template/$datasetname.xlsx");
    }


    public function getPlanByAgentID($id)
    {
        $data['plans'] = DB::table('plan_formations')->whereIn("id",AgentPlan::select('id_plan')->where('id_agent',$id)->get())
                    ->get(["id","annee_debut","annee_fin"]);
        
                    return response()->json($data);
    }

}

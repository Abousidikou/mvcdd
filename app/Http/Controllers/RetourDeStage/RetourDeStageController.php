<?php

namespace App\Http\Controllers\RetourDeStage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use App\Http\Controllers\AgentFormation\AgentFormationController;
use App\Http\Controllers\MiseEnStage\MiseEnStageController;
use App\Models\RetourDeStage;
use App\Models\MiseEnStage;
use App\Models\Structures;
use App\Models\AgentFormation;
use Illuminate\Support\Facades\DB;
use App\Models\Level;

class RetourDeStageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        $r_stages = RetourDeStage::all();
        return view('admin.retour_stage.data',compact('r_stages'));
    }

    public function form_insert()
    {
        $agents_ids = MiseEnStage::pluck('id_agent')->toArray();
        $agents = AgentFormation::whereIn('id',$agents_ids)->get();
        $id_cat =  DB::table('type')->select('id')->where('wording','Categorie')->first();
        $categories = Level::where('id_type',$id_cat->id)->get();
        $id_structure =  DB::table('type')->select('id')->where('wording','Structure')->first();
        $structures = Level::where('id_type',$id_structure->id)->get();
        return view('admin.retour_stage.form_insert',compact('agents','structures','categories'));
    }

    public function insert(Request $request)
    {
        $rules = [
            'id_agent' =>'required'
        ];
 
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails())
        {
            return redirect()->back()->with('validation','error');
        }
        else
        {
            
            $mise_r = new RetourDeStage;
            $mise_r->id_agent = $request->id_agent;
            $mise_r->numero_decision_rs = $request->numero_decision_rs;
            $mise_r->date_signature = $request->date_signature;
            $mise_r->date_fin_formation = $request->date_fin_formation;
            $mise_r->date_reprise_service = $request->date_reprise_service;
            $mise_r->categorie_rs = $request->categorie_rs;
            $mise_r->annee_rs = $request->annee_rs;
            $mise_r->incidence_bn = $request->incidence_bn;
            $mise_r->structure_rs = $request->structure_rs;
            if($mise_r->save())
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
        $m_s = RetourDeStage::where('id',$id)->get();
        if(!$m_s->isEmpty())
        {
            $m_sIsDeleted = RetourDeStage::find($id)->delete();
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
        $retour_stage = RetourDeStage::where('id',$request->id)->first();
        $id_cat =  DB::table('type')->select('id')->where('wording','Categorie')->first();
        $categories = Level::where('id_type',$id_cat->id)->get();
        $agents = AgentFormation::all();
        $structures = Structures::all();
        return view('admin.retour_stage.form_update',compact('categories','agents','retour_stage','structures'));
    }

    public function update(Request $request)
    {
        $rules = [
            'id_agent' =>'required'
        ];
 
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails())
        {
            return redirect()->back()->with('validation','error');
        }
        else
        {
            $mise_r = RetourDeStage::where('id',$request->id)->first();
            $mise_r->id_agent = $request->id_agent;
            $mise_r->numero_decision_rs = $request->numero_decision_rs;
            $mise_r->date_signature = $request->date_signature;
            $mise_r->date_fin_formation = $request->date_fin_formation;
            $mise_r->date_reprise_service = $request->date_reprise_service;
            $mise_r->categorie_rs = $request->categorie_rs;
            $mise_r->annee_rs = $request->annee_rs;
            $mise_r->incidence_bn = $request->incidence_bn;
            $mise_r->structure_rs = $request->structure_rs;
            if($mise_r->save())
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
        return view('admin.retour_stage.form_import',compact('structures'));
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
            $retour_stage = $spreadsheet->getSheet(($numberOfSheet-1));

            $numberOfRow = $retour_stage->getHighestRow();
  
            // $highestColumn = $domainsheet->getHighestColumn(); // e.g 'F'
            // $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn); // e.g. 5

            for($i = 2; $i <= $numberOfRow; $i++)
            {
                
                $currentMatricule = $retour_stage->getCellByColumnAndRow(1,$i)->getValue();
                if(!is_int($currentMatricule)) $currentMatricule =  $retour_stage->getCellByColumnAndRow(1,$i)->getCalculatedValue();
                $numero_decision_rs = $retour_stage->getCellByColumnAndRow(3,$i)->getValue();
                $date_signature = $retour_stage->getCellByColumnAndRow(4,$i)->getValue();
                $date_fin_formation =  $retour_stage->getCellByColumnAndRow(5,$i)->getValue();
                $date_reprise_service = $retour_stage->getCellByColumnAndRow(6,$i)->getValue();
                $categorieCode = $retour_stage->getCellByColumnAndRow(7,$i)->getValue();
                if(!is_int($categorieCode)) $categorieCode =  $retour_stage->getCellByColumnAndRow(7,$i)->getCalculatedValue();
                $anneeCode = $retour_stage->getCellByColumnAndRow(9,$i)->getValue();
                if(!is_int($anneeCode)) $anneeCode =  $retour_stage->getCellByColumnAndRow(9,$i)->getCalculatedValue();
                $incidence_bn = $retour_stage->getCellByColumnAndRow(11,$i)->getValue();
                $structureCode = $retour_stage->getCellByColumnAndRow(12,$i)->getValue();
                if(!is_int($structureCode)) $structureCode =  $retour_stage->getCellByColumnAndRow(12,$i)->getCalculatedValue();

                if($currentMatricule == null) break;
                $categorie = "";
                if ($categorieCode != null) {
                    if($categorieCode instanceof RichText)
                    {
                        $categorieCode = $categorieCode->getPlainText();
                    }
                    else
                    {
                        $categorieCode = (int)$categorieCode;
                    }
                    //Formating
                    
                    $cat = Level::where('id',$categorieCode)->first();
                    if($cat == null){
                        $NotCorrect = "La categorie  fournie à la ligne ".$i." n'est pas dans la base.  <br> Veillez retirer du fichier les lignes précédentes car déjà <em>enrégistrées</em> ou vous courrez le risque d'avoir des doublons dans la base.";
                        return redirect()->back()->with('nomenclatureError',$NotCorrect);
                    }
                    $categorie = $cat->wording;
                }else {
                    $categorie = "null";
                }
                
                $ann = "";
                if ($anneeCode  != null) {
                    if($anneeCode instanceof RichText)
                    {
                        $anneeCode = $anneeCode->getPlainText();
                    }
                    else
                    {
                        $anneeCode = (string)$anneeCode;
                    }
                    
                    //Formation
                    $annee = DB::table('annee')->where('id',$anneeCode)->first();
                    if ($annee == null) {
                        $NotCorrect = "L'annee  fournie à la ligne ".$i." n'est pas correct.  <br> Veillez retirer du fichier les lignes précédentes car déjà <em>enrégistrées</em> ou vous courrez le risque d'avoir des doublons dans la base.";
                        return redirect()->back()->with('nomenclatureError',$NotCorrect);
                    }
                    $ann = $annee->value;
                } else {
                    if ($anneeCode == null) {
                        $NotCorrect = "anneeCode au niveau de la ligne ".$i." n'est pas correct ou est null.  <br> Veillez retirer du fichier les lignes précédentes car déjà <em>enrégistrées</em> ou vous courrez le risque d'avoir des doublons dans la base.";
                        return redirect()->back()->with('nomenclatureError',$NotCorrect);
                    }
                }
                


                if ($structureCode != null) {
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
                    $structureName = $struct->wording;
                } else {
                    $structureName = "null";
                }
                


                if($incidence_bn instanceof RichText)
                {
                    $incidence_bn = $incidence_bn->getPlainText();
                }
                else
                {
                    $incidence_bn = (string)$incidence_bn;
                }

                if($date_reprise_service instanceof RichText)
                {
                    $date_reprise_service = $date_reprise_service->getPlainText();
                }
                else
                {
                    $date_reprise_service = (string)$date_reprise_service;
                }

                if($date_fin_formation instanceof RichText)
                {
                    $date_fin_formation = $date_fin_formation->getPlainText();
                }
                else
                {
                    $date_fin_formation = (string)$date_fin_formation;
                }

                if($date_signature instanceof RichText)
                {
                    $date_signature = $date_signature->getPlainText();
                }
                else
                {
                    $date_signature = (string)$date_signature;
                }



                if($numero_decision_rs instanceof RichText)
                {
                    $numero_decision_rs = $numero_decision_rs->getPlainText();
                }
                else
                {
                    $numero_decision_rs = (string)$numero_decision_rs;
                }


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
                    $errormsg = "l'Agent n'existe pas ou n'est pas en stage.";
                    $linesWithError[] = $i;
                }
                else
                {
                    $id_agent = AgentFormation::where('matricule',$currentMatricule)->first();
                    $mise_r = new RetourDeStage;
                    $mise_r->id_agent = $id_agent->id;
                    $mise_r->numero_decision_rs = $numero_decision_rs;
                    $mise_r->date_signature = $date_signature;
                    $mise_r->date_fin_formation = $date_fin_formation;
                    $mise_r->date_reprise_service = $date_reprise_service;
                    $mise_r->categorie_rs = $categorie;
                    $mise_r->annee_rs = $ann;
                    $mise_r->incidence_bn = $incidence_bn;
                    $mise_r->structure_rs = $structureName;
                    if($mise_r->save())
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
        
        if($agent != null)
        {
            $mise = MiseEnStage::where('id_agent',$agent->id)->first();
            if($mise == null){
                return false;
            }else{
                return true;
            }
            
        }
        return false;
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
        $retour_stage = $spreadsheet->getActiveSheet();
        $mise_r = RetourDeStage::all();
        $datasetname = "retour_de_stage";
        $retour_stage->getCellByColumnAndRow(1,1)->setValue("Matricule")->getStyle()->getFont()->setBold(true);
        $retour_stage->getCellByColumnAndRow(2,1)->setValue("Nom & Prenoms")->getStyle()->getFont()->setBold(true);
        $retour_stage->getCellByColumnAndRow(3,1)->setValue("Numéro de référence")->getStyle()->getFont()->setBold(true);
        $retour_stage->getCellByColumnAndRow(4,1)->setValue("Date signature de retour en stage")->getStyle()->getFont()->setBold(true);
        $retour_stage->getCellByColumnAndRow(5,1)->setValue("Date de fin de formation")->getStyle()->getFont()->setBold(true);
        $retour_stage->getCellByColumnAndRow(6,1)->setValue("Date de reprise de service")->getStyle()->getFont()->setBold(true);
        $retour_stage->getCellByColumnAndRow(7,1)->setValue("Catégorie RS")->getStyle()->getFont()->setBold(true);
        $retour_stage->getCellByColumnAndRow(8,1)->setValue("Année RS")->getStyle()->getFont()->setBold(true);
        $retour_stage->getCellByColumnAndRow(9,1)->setValue("Incidence BN")->getStyle()->getFont()->setBold(true);
        $retour_stage->getCellByColumnAndRow(10,1)->setValue("Structure RS")->getStyle()->getFont()->setBold(true);

        $i = 2;
        foreach($mise_r as $mise)
        {
            $retour_stage->getCellByColumnAndRow(1,$i)->setValue($mise->agent->matricule);
            $retour_stage->getCellByColumnAndRow(2,$i)->setValue($mise->agent->nom_prenoms);
            $retour_stage->getCellByColumnAndRow(3,$i)->setValue($mise->numero_decision_rs);
            $d1 = '';
            if ($mise->date_signature != null && $mise->date_signature!= 'null' && $mise->date_signature != "") {
                $d1 = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($mise->date_signature)->format('j/m/Y');
            }else{
                $d1 = $mise->date_signature;
            }
            $retour_stage->getCellByColumnAndRow(4,$i)->setValue($d1);
            if ($mise->date_fin_formation != null && $mise->date_fin_formation!= 'null' && $mise->date_fin_formation != "") {
                $d1 = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($mise->date_fin_formation)->format('j/m/Y');
            }else{
                $d1 = $mise->date_fin_formation;
            }
            $retour_stage->getCellByColumnAndRow(5,$i)->setValue($d1);
            if ($mise->date_reprise_service != null && $mise->date_reprise_service!= 'null' && $mise->date_reprise_service != "") {
                $d1 = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($mise->date_reprise_service)->format('j/m/Y');
            }else{
                $d1 = $mise->date_reprise_service;
            }
            $retour_stage->getCellByColumnAndRow(6,$i)->setValue($d1);
            $retour_stage->getCellByColumnAndRow(7,$i)->setValue($mise->categorie_rs);
            $retour_stage->getCellByColumnAndRow(8,$i)->setValue($mise->annee_rs);
            $retour_stage->getCellByColumnAndRow(9,$i)->setValue($mise->incidence_bn);
            $retour_stage->getCellByColumnAndRow(10,$i)->setValue($mise->structure_rs);
            $i++;
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $path = public_path();
        $path = $path."/template/$datasetname.xlsx";
        $writer->save($path);

        return redirect()->back()->with("path","template/$datasetname.xlsx");
    }


    public function buildoldRetour(Spreadsheet $spreadsheet){
        $datasetSheet = new Worksheet($spreadsheet,'Liste Retours en Stage');
        $spreadsheet->addSheet($datasetSheet, 0);
        
        $datasetSheet = $spreadsheet->getSheet(0);

        
        $datasetSheet->getCellByColumnAndRow(1,1)->setValue("Matricule")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(2,1)->setValue("Nom & Prenoms")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(3,1)->setValue("Numéro de référence")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(4,1)->setValue("Date signature de mise en stage")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(5,1)->setValue("Date de fin de formation")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(6,1)->setValue("Date de reprise de service")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(7,1)->setValue("Catégorie RS")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(8,1)->setValue("Année RS")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(9,1)->setValue("Incidence BN")->getStyle()->getFont()->setBold(true);
        $datasetSheet->getCellByColumnAndRow(10,1)->setValue("Structure RS")->getStyle()->getFont()->setBold(true);

        $mise_r = RetourDeStage::all();
        $i = 2;
        foreach($mise_r as $mise)
        {
            $datasetSheet->getCellByColumnAndRow(1,$i)->setValue($mise->agent->matricule);
            $datasetSheet->getCellByColumnAndRow(2,$i)->setValue($mise->agent->nom_prenoms);
            $datasetSheet->getCellByColumnAndRow(3,$i)->setValue($mise->numero_decision_rs);
            $datasetSheet->getCellByColumnAndRow(4,$i)->setValue($mise->date_signature);
            $datasetSheet->getCellByColumnAndRow(5,$i)->setValue($mise->date_fin_formation);
            $datasetSheet->getCellByColumnAndRow(6,$i)->setValue($mise->date_reprise_service);
            $datasetSheet->getCellByColumnAndRow(7,$i)->setValue($mise->categorie_rs);
            $datasetSheet->getCellByColumnAndRow(8,$i)->setValue($mise->annee_rs);
            $datasetSheet->getCellByColumnAndRow(9,$i)->setValue($mise->incidence_bn);
            $datasetSheet->getCellByColumnAndRow(10,$i)->setValue($mise->structure_rs);
            $i++;
        }


        return $spreadsheet;
    }

    public function genererExp()
    {
        $spreadsheet = new Spreadsheet();
        $build = new AgentFormationController();
        $buildMise = new MiseEnStageController();
        $spreadsheet = new Spreadsheet();
        $spreadsheet = $this->buildoldRetour($spreadsheet);
        $spreadsheet = $buildMise->buildoldMise($spreadsheet);
        $spreadsheet = $build->buildAnnee($spreadsheet);
        $spreadsheet = $build->buildCategorie($spreadsheet);
        $spreadsheet = $build->buildStructure($spreadsheet);
        $retour_stage = $spreadsheet->getActiveSheet();
        $retour_stage->setTitle("retour_de_stage");
        $datasetname = "retour_de_stage";
        $retour_stage->getCellByColumnAndRow(1,1)->setValue("Matricule")->getStyle()->getFont()->setBold(true);
        $retour_stage->getCellByColumnAndRow(2,1)->setValue("Nom & Prenoms")->getStyle()->getFont()->setBold(true);
        $retour_stage->getCellByColumnAndRow(3,1)->setValue("Numéro de référence")->getStyle()->getFont()->setBold(true);
        $retour_stage->getCellByColumnAndRow(4,1)->setValue("Date signature de mise en stage")->getStyle()->getFont()->setBold(true);
        $retour_stage->getCellByColumnAndRow(5,1)->setValue("Date de fin de formation")->getStyle()->getFont()->setBold(true);
        $retour_stage->getCellByColumnAndRow(6,1)->setValue("Date de reprise de service")->getStyle()->getFont()->setBold(true);
        $retour_stage->getCellByColumnAndRow(7,1)->setValue("Catégorie Code")->getStyle()->getFont()->setBold(true);
        $retour_stage->getCellByColumnAndRow(8,1)->setValue("Catégorie RS")->getStyle()->getFont()->setBold(true);
        $retour_stage->getCellByColumnAndRow(9,1)->setValue("Année Code ")->getStyle()->getFont()->setBold(true);
        $retour_stage->getCellByColumnAndRow(10,1)->setValue("Année RS")->getStyle()->getFont()->setBold(true);
        $retour_stage->getCellByColumnAndRow(11,1)->setValue("Incidence BN")->getStyle()->getFont()->setBold(true);
        $retour_stage->getCellByColumnAndRow(12,1)->setValue("StructureCode")->getStyle()->getFont()->setBold(true);
        $retour_stage->getCellByColumnAndRow(13,1)->setValue("Structure RS")->getStyle()->getFont()->setBold(true);


        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $path = public_path();
        $path = $path."/template/$datasetname.xlsx";
        $writer->save($path);

        return redirect()->back()->with("examplaire","template/$datasetname.xlsx");
    }
}

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
            $isagent = AgentFormation::where('id',$request->id_agent)->first();
            $ismise = MiseEnStage::where('id_agent',$request->id_agent)->first();
            if($isagent == null || $ismise == null)
            {
                return redirect()->back()->with('agents_not_exist','error');
            }
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
                $numero_decision_rs = $retour_stage->getCellByColumnAndRow(3,$i)->getValue();
                $date_signature = $retour_stage->getCellByColumnAndRow(4,$i)->getValue();
                $date_fin_formation =  $retour_stage->getCellByColumnAndRow(5,$i)->getValue();
                $date_reprise_service = $retour_stage->getCellByColumnAndRow(6,$i)->getValue();
                $categorieCode = $retour_stage->getCellByColumnAndRow(7,$i)->getValue();
                $anneeCode = $retour_stage->getCellByColumnAndRow(9,$i)->getValue();
                $incidence_bn = $retour_stage->getCellByColumnAndRow(11,$i)->getValue();
                $structureCode = $retour_stage->getCellByColumnAndRow(12,$i)->getValue();

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
                        return redirect()->back()->with('nomenclatureError','error');
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
                        return redirect()->back()->with('nomenclatureError','error');
                    }
                    $ann = $annee->value;
                } else {
                    $ann == "null";
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
                        return redirect()->back()->with('nomenclatureError','error');
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
                                        ->with('warning','warning');
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
        $mised = MiseEnStage::where('id_agent',$agent->id)->count();
        if($mised != 0)
        {
            $retoured = RetourDeStage::where('id_agent',$agent->id)->count();
            if($retoured == 0) {
                return true;
            }

            if ($mised - $retoured == 1)  {
                return true;
            }

            return false;
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
        $retour_stage->getCellByColumnAndRow(1,1)->setValue("Matricule");
        $retour_stage->getCellByColumnAndRow(2,1)->setValue("Nom & Prenoms");
        $retour_stage->getCellByColumnAndRow(3,1)->setValue("Numéro de decision de retour de stage");
        $retour_stage->getCellByColumnAndRow(4,1)->setValue("Date signature de mise en stage");
        $retour_stage->getCellByColumnAndRow(5,1)->setValue("Date de fin de formation");
        $retour_stage->getCellByColumnAndRow(6,1)->setValue("Date de reprise de service");
        $retour_stage->getCellByColumnAndRow(7,1)->setValue("Catégorie RS");
        $retour_stage->getCellByColumnAndRow(8,1)->setValue("Année RS");
        $retour_stage->getCellByColumnAndRow(9,1)->setValue("Incidence BN");
        $retour_stage->getCellByColumnAndRow(10,1)->setValue("Structure RS");

        $i = 2;
        foreach($mise_r as $mise)
        {
            $retour_stage->getCellByColumnAndRow(1,$i)->setValue($mise->agent->matricule);
            $retour_stage->getCellByColumnAndRow(2,$i)->setValue($mise->agent->nom_prenoms);
            $retour_stage->getCellByColumnAndRow(3,$i)->setValue($mise->numero_decision_rs);
            $retour_stage->getCellByColumnAndRow(4,$i)->setValue($mise->date_signature);
            $retour_stage->getCellByColumnAndRow(5,$i)->setValue($mise->date_fin_formation);
            $retour_stage->getCellByColumnAndRow(6,$i)->setValue($mise->date_reprise_service);
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

        
        $datasetSheet->getCellByColumnAndRow(1,1)->setValue("Matricule");
        $datasetSheet->getCellByColumnAndRow(2,1)->setValue("Nom & Prenoms");
        $datasetSheet->getCellByColumnAndRow(3,1)->setValue("Numéro de decision de retour de stage");
        $datasetSheet->getCellByColumnAndRow(4,1)->setValue("Date signature de mise en stage");
        $datasetSheet->getCellByColumnAndRow(5,1)->setValue("Date de fin de formation");
        $datasetSheet->getCellByColumnAndRow(6,1)->setValue("Date de reprise de service");
        $datasetSheet->getCellByColumnAndRow(7,1)->setValue("Catégorie RS");
        $datasetSheet->getCellByColumnAndRow(8,1)->setValue("Année RS");
        $datasetSheet->getCellByColumnAndRow(9,1)->setValue("Incidence BN");
        $datasetSheet->getCellByColumnAndRow(10,1)->setValue("Structure RS");

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
        $retour_stage->getCellByColumnAndRow(1,1)->setValue("Matricule");
        $retour_stage->getCellByColumnAndRow(2,1)->setValue("Nom & Prenoms");
        $retour_stage->getCellByColumnAndRow(3,1)->setValue("Numéro de decision de retour de stage");
        $retour_stage->getCellByColumnAndRow(4,1)->setValue("Date signature de mise en stage");
        $retour_stage->getCellByColumnAndRow(5,1)->setValue("Date de fin de formation");
        $retour_stage->getCellByColumnAndRow(6,1)->setValue("Date de reprise de service");
        $retour_stage->getCellByColumnAndRow(7,1)->setValue("Catégorie Code");
        $retour_stage->getCellByColumnAndRow(8,1)->setValue("Catégorie RS");
        $retour_stage->getCellByColumnAndRow(9,1)->setValue("Année Code ");
        $retour_stage->getCellByColumnAndRow(10,1)->setValue("Année RS");
        $retour_stage->getCellByColumnAndRow(11,1)->setValue("Incidence BN");
        $retour_stage->getCellByColumnAndRow(12,1)->setValue("StructureCode");
        $retour_stage->getCellByColumnAndRow(13,1)->setValue("Structure RS");


        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $path = public_path();
        $path = $path."/template/$datasetname.xlsx";
        $writer->save($path);

        return redirect()->back()->with("examplaire","template/$datasetname.xlsx");
    }
}

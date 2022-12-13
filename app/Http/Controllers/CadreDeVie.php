<?php

namespace App\Http\Controllers;

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


class CadreDeVie extends Controller
{
    public function formImport()
    {
        $id_structure =  DB::table('type')->select('id')->where('wording','Structure')->first();
        $structures = Level::where('id_type',$id_structure->id)->get();
        return view('admin.agentFormation.formImport_agents',compact('structures'));
    }


    public function postcdv(Request $request)
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
            $agentsheet = $spreadsheet->getSheet(5);
            $numberOfRow = $agentsheet->getHighestRow();
            
            // $highestColumn = $domainsheet->getHighestColumn(); // e.g 'F'
            // $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn); // e.g. 5


            dd($numberOfSheet,$numberOfRow,$agentsheet->getTitle());
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
}

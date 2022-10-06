<?php

namespace App\Http\Controllers\Infos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MiscController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Level;
use App\Models\Indicators;
use App\Models\Data;
use App\Models\Type;
use App\Models\Structures;
use App\Models\SubDomain;
use App\Models\Domain;
use App\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class InfosController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['showMoreDetails', 'showMoreLevel']]);
        //$this->middleware('auth');
    }

    public function index(Request $request)
    {
        $last_id = intval($request->get('last_id', '0'));
        $infos = null;
        $isPlus = true;
        $domain = null;
        $off = 500;
        if(Auth::user()->id_structure != NULL) {
            $total = Data::where('id_user', Auth::user()->id)->count();
            $infos = Data::where('id_user', Auth::user()->id)->orderBy('id','asc')->offset($last_id * $off)->limit($off)->get();
            $isPlus = (($total - ($last_id * $off) - $infos->count()) >  0) ? true : false;
            $domain = Auth::user()->structure->subdomain[0]->domain->wording;
        }else{
            $total = Data::count();
            $infos = Data::orderBy('id','asc')->offset($last_id * $off)->limit($off)->get();
            $isPlus = (($total - ($last_id * $off) - $infos->count()) >  0) ? true : false;
            $domain = null;
        }

        //dd($infos);

        foreach($infos as $info)
        {
            $info->userfn = $info->user->name; 
            $info->indicat = $info->indicator->wording; 

            $numberOfLevel = $info->levels()->count();
            if($numberOfLevel == 0)
            {
                $info->levelof = "Aucun niveau";
                $info->multilevel = false;
            }
            else if($numberOfLevel == 1)
            {   
                $info->levelof = $info->levels()->first()->wording;
                $info->multilevel = false;
            }
            else
            {
                $info->levelof = $info->levels()->first()->wording;
                $info->multilevel = true;
            } 

            $info->subd = $info->indicator->subdomain->wording;
            $info->date_start = date_format(date_create($info->date_start),'d-m-Y');
            $info->date_end = date_format(date_create($info->date_end),'d-m-Y');
        }
        return view('admin.info.infos',compact('infos', 'domain','last_id','isPlus'));
    }

    public function showMoreDetails(Request $request)
    {
        if($request->ajax())
        {
            $infoId = $request->infoId;
            $info = Data::find($infoId);
            
            $info->date_start = date_format(date_create($info->date_start),'d-m-Y');
            $info->date_end = date_format(date_create($info->date_end),'d-m-Y');

            $builtList = "<table class='table table-bordered table-striped table-condensed'>";

            $builtList .= "<thead>
                <tr>
                    <th>Periode</th>
                    <th>Periodicite</th>
                    <th>Observation</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>$info->date_start a $info->date_end</td>
                    <td>$info->periodicity</td>
                    <td>$info->observation</td>
                </tr>
            </tbody>
            </table>
            ";

            return json_encode(array('title' => "", 'builtList' => $builtList));
        }
    }

    public function showMoreLevel(Request $request)
    {
        if($request->ajax())
        {
            $infoId = $request->infoId;
            $levels = Data::find($infoId)->levels()->get();
            
            //$structWording = Structures::where('id',$structId)->first()->wording;
            $builtList = "<ol>";
            foreach($levels as $level)
            {
                $builtList .= "<li>$level->wording</li>";
            }

            $builtList .= "</ol>";

            return json_encode(array('title' => '', 'builtList' => $builtList));
        }
    }

    public function form()
    {
        $user = Auth::user();
        $levelsOptions = "";
        $indicatorsOptions = "";

        $levels = Level::all();
        $indicators = Indicators::all(); 

        foreach($levels as $level)
        {
            $levelsOptions .= "<option value=$level->id>$level->wording</option>";
        }

        foreach($indicators as $indicator)
        {
            $indicatorsOptions .= "<option value=$indicator->id>$indicator->wording</option>";
        }


        $subdomains = SubDomain::all();
        $subdomainsOptions = "";

        if($user->id_structure != null) {
            $subdomains = $user->structure->subdomain;
        }

        foreach($subdomains as $subdomain)
        {
            $subdomainsOptions .= "<option value=$subdomain->id>$subdomain->wording</option>";
        }

        $domains = Domain::all();
        $domainsOptions = "";

        foreach($domains as $domain)
        {
            $domainsOptions .= "<option value=$domain->id>$domain->wording</option>";
        }

        
        return view('admin.info.new_info', compact('levelsOptions','indicatorsOptions','domainsOptions','subdomainsOptions', 'user'));
    } 

    public function create(Request $request)
    {
        $levels = $request->input('levels');
        $selectedChk = MiscController::chkArrayOfMultiSelect($levels);
        
        $number = $request->number;
        $date_start = $request->date_start; 
        $date_end = $request->date_end;
        $observation = $request->observation;
        $year = $request->year;
        $periodicity = $request->periodicity;
        $indicator = $request->indicator;
 
        $rules = [
            'number' => 'required',
            'date_start' => 'required',
            'date_end' => 'required',
            'year' => 'required',
            'periodicity' => 'required',
            'indicator' => 'required',
            'observation' => 'required'
        ];

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails() || $selectedChk == null)
        {
            return redirect()->back()->with('validation','error');
        } 
        else
        {

            $userId = Auth::user()->id;
            $data = new Data;
            //$data->id_level = $level; 
            $data->id_indicator = $indicator; 
            $data->id_user = $userId;
            $data->value = $number;
            $data->periodicity = $periodicity;
            $data->year = $year;
            $data->date_start = MiscController::toMysqlDate($date_start);
            $data->date_end =  MiscController::toMysqlDate($date_end);
            $data->observation = $observation;

            if($data->save())
            {
                if($selectedChk!=null)
                {
                    $data->levels()->attach($selectedChk);
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
        $info_id = $id; 

        $infoIsDeleted = Data::find($info_id)->delete();
        if($infoIsDeleted)
        {
            return redirect()->back()->with('success','success');
        }
        else
        {
            return redirect()->back()->with('error','error');
        }
    }

    public function prepareUpdate($id)
    {
        $info = Data::where('id',$id)->first();
        $info->date_start = date_format(date_create($info->date_start),'d/m/Y');
        $info->date_end = date_format(date_create($info->date_end),'d/m/Y');

        // $levelId = $info->level->id;
        // $levelWording = $info->level->wording; 

        $indicatorId = $info->indicator->id;
        $indicatorWording = $info->indicator->wording;

        $info->indicat = $info->indicator->wording;
        // $info->struct = $info->level->indicators->subdomain->wording;
        // $levelsOptions = "";
        // $levelsOptions .= "<option value=$levelId>$levelWording</option>";

        $indicatorsOptions = "";
        $indicatorsOptions .= "<option value=$indicatorId selected>$indicatorWording</option>";
        
        //$levels = Level::where('id','!=',$levelId)->get();

        $indicators = Indicators::where('id','!=',$indicatorId)->get();

        foreach($indicators as $indicator)
        {
            $indicatorsOptions .= "<option value=$indicator->id>$indicator->wording</option>";
        }

        $subdomainId = $info->indicator->id_subdomain;
        $subdomainWording = $info->indicator->subdomain->wording;
        

        $subdomains = SubDomain::where('id', '!=', $subdomainId)->get();
        $subdomainsOptions = "";
        $subdomainsOptions .= "<option value=$subdomainId>$subdomainWording</option>";

        foreach($subdomains as $subdomain)
        {
            $subdomainsOptions .= "<option value=$subdomain->id>$subdomain->wording</option>";
        }

        $domainId = $info->indicator->subdomain->id_domain;
        $domainWording = $info->indicator->subdomain->domain->wording;

        $domains = Domain::where('id', '!=', $domainId)->get();

        $domainsOptions = "";

        $domainsOptions .= "<option value=$domainId>$domainWording</option>";

        foreach($domains as $domain)
        {
            $domainsOptions .= "<option value=$domain->id>$domain->wording</option>";
        }

        return view('admin.info.update_info', compact('indicatorsOptions','info','domainsOptions','subdomainsOptions'));
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $levels = $request->input('levels');
        $selectedChk = MiscController::chkArrayOfMultiSelect($levels);
        $indicator = $request->indicator;
        $number = $request->number;
        $periodicity = $request->periodicity;
        $year = $request->year;
        $date_start = $request->date_start; 
        $date_end = $request->date_end;
        $observation = $request->observation;
 
        $rules = [
            'number' => 'required',
            'date_start' => 'required',
            'date_end' => 'required',
            'observation' => 'required',
            'periodicity' => 'required',
            'indicator' => 'required',
            'year' => 'required'
        ];

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails())
        {
            return redirect()->back()->with('validation','error');
        } 
        else
        {
            $userId = Auth::user()->id;

            $updateArray = [
                'id_user' => $userId,
                'id_indicator' => $indicator,
                'value' => $number,
                'periodicity' => $periodicity,
                'year' => $year,
                'date_start' => MiscController::toMysqlDate($date_start),
                'date_end' => MiscController::toMysqlDate($date_end),
                'observation' => $observation,
            ];

            $dataIsUpdated = Data::find($id)->update($updateArray);
            if($dataIsUpdated)
            {
                if($selectedChk!=null)
                {
                    Data::find($id)->levels()->sync($selectedChk);
                }
                return redirect()->back()->with('success','success');
            }
            else
            {
                return redirect()->back()->with('error','error');
            }
        }
    }

    public function exportForm(Request $request)
    {

        $domains = Domain::all();
        $domainsOptions = "";

        foreach($domains as $domain)
        { 
            $domainsOptions .= "<option value=$domain->id>$domain->wording</option>";
        }

        return view('admin.info.export_info', compact('domainsOptions'));
    }

    public function importForm(Request $request)
    {
        $domains = Domain::all();
        $domainsOptions = "";

        foreach($domains as $domain)
        { 
            $domainsOptions .= "<option value=$domain->id>$domain->wording</option>";
        }

        return view('admin.info.import_info', compact('domainsOptions'));
    }

    public function importFile()
    {
        return view('admin.info.import_info_file');
    }

    public function export(Request $request)
    {
        //We retrieve indicators and types as Array;
        $indicators = $request->input('indicators');
        $types = $request->input('types');
        $datasetname = $request->datasetname;
        $indicators = MiscController::chkArrayOfMultiSelect($indicators);
        $types = MiscController::chkArrayOfMultiSelect($types);
        $spreadsheet = new Spreadsheet();
        
        
        if(null != $indicators && null != $types)
        {
            //We first build dataset worksheet;
            $spreadsheet = $this->buildDatasetSheet($indicators, $datasetname, $types, $spreadsheet);
            $spreadsheet = $this->buildIndicatorsSheet($indicators, $spreadsheet);
            $spreadsheet = $this->buildTypesSheet($types, $spreadsheet);
            $spreadsheet = $this->buildDataSheet($indicators, $types, $spreadsheet);
            
            $spreadsheet->setActiveSheetIndex(0);

            $writer = new Xlsx($spreadsheet);
            $path = public_path();
            $path = $path."/template/$datasetname.xlsx";
            $writer->save($path);

            return redirect()->back()->with("path","template/$datasetname.xlsx");
        }
        else
        {

        }
    }

    public function buildDatasetSheet($indicators, $datasetname, $types, Spreadsheet $spreadsheet)
    {
        $datasetSheet = new Worksheet($spreadsheet,"Dataset");
        $spreadsheet->addSheet($datasetSheet, 0);

        $datasetSheet = $spreadsheet->getSheet(0);
        $datasetSheet->setCellValue('A1','name');
        $datasetSheet->setCellValue('B1','value');

        $datasetSheet->setCellValue('A2','dataset name');
        $datasetSheet->setCellValue('B2',$datasetname);

        $datasetSheet->setCellValue('A3','dimensions');
        $dim = $this->buildDatasetDim($types);
        $datasetSheet->setCellValue('B3',$dim);

        $datasetSheet->setCellValue('A4','data');
        $datasetSheet->setCellValue('B4','donnees');

        return $spreadsheet;

    }

    public function buildDatasetDim($types)
    {
        $dim = "Indicateurs;";
        foreach($types as $type)
        {
            $typeWording = Type::find($type)->wording;
            $dim .= $typeWording;
            $dim .= ";";
        }

        return $dim;
        
    }

    public function buildIndicatorsSheet($indicators, Spreadsheet $spreadsheet)
    {
        $indicatorsSheet = new Worksheet($spreadsheet,"Indicateurs");
        $spreadsheet->addSheet($indicatorsSheet, 1);

        $indicatorsSheet = $spreadsheet->getSheet(1);

        $indicatorsSheet->setCellValue('A1','code');
        $indicatorsSheet->setCellValue('B1','name');
        $indicatorsSheet->setCellValue('C1','order');
        $indicatorsSheet->setCellValue('D1','parent');
        $indicatorsSheet->setCellValue('E1','notes');

        $i = 2;
        foreach($indicators as $indicator)
        {
            $indicator = Indicators::find($indicator);

            $indicatorsSheet->setCellValue('A'.$i, $indicator->id);
            $indicatorsSheet->setCellValue('B'.$i, $indicator->wording);
            $indicatorsSheet->setCellValue('C'.$i, ($i-1));
            $indicatorsSheet->setCellValue('D'.$i,'');
            $indicatorsSheet->setCellValue('E'.$i,'');

            $i++;
        }


        return $spreadsheet;
    }

    public function buildTypesSheet($types, Spreadsheet $spreadsheet)
    {
        foreach($types as $type)
        {
            $typeWording = Type::find($type)->wording;
            $levels = Level::where('id_type','=',$type)->get();
            
            $typeSheet = new Worksheet($spreadsheet,$typeWording);
            $numberOfSheet = $spreadsheet->getSheetCount() - 1;
            $spreadsheet->addSheet($typeSheet, $numberOfSheet);

            $typeSheet = $spreadsheet->getSheet($numberOfSheet);

            $typeSheet->setCellValue('A1','code');
            $typeSheet->setCellValue('B1','name');
            $typeSheet->setCellValue('C1','order');
            $typeSheet->setCellValue('D1','parent');
            $typeSheet->setCellValue('E1','notes');

            $i = 2;
            foreach($levels as $level)
            {
                if($level->wording == "null" || $level->wording == null ){
                    continue;
                 }
                $typeSheet->setCellValue('A'.$i, $level->id);
                $typeSheet->setCellValue('B'.$i, $level->wording);
                $typeSheet->setCellValue('C'.$i, ($i-1));
                $typeSheet->setCellValue('D'.$i,'');
                $typeSheet->setCellValue('E'.$i,'');

                $i++;
            }

        }

        return $spreadsheet;
    }


    public function buildTypesSheetForImport($types, $indicators, Spreadsheet $spreadsheet)
    {
        $existLevels = [];
        foreach($types as $type)
        {
            $typeWording = Type::find($type)->wording;
            $levels = Level::where('id_type','=',$type)->get();
            $realLevels = [];
            foreach($indicators as $indicator)
            { 
                $indicatorI = Indicators::find($indicator);
                $linkedLevels = $indicatorI->levels()->get();

                foreach($levels as $level)
                {
                    foreach($linkedLevels as $lklevel)
                    {
                        if($lklevel->id == $level->id && (!in_array($level->id, $existLevels)))
                        {
                            // $wasfound = false;
                            // foreach($existLevels as $elevel)
                            // {
                            //     if($lklevel->id == $level->id)
                            //     {
                            //         $wasfound = true;
                            //         break;
                            //     }
                            //     else
                            //     {
                            //         continue;
                            //     }
                            // }

                            // if($wasfound == true)
                            // {
                            //     break;
                            // }
                            // else
                            // {
                            //     $realLevels[] = $level;
                            //     $existLevels[] = $level;
                            //     break;
                            // }
                            $realLevels[] = $level;
                            $existLevels[] = $level->id;
                            break;
                        }
                        else
                        {
                            continue;
                        }
                    }
                }
            }

            $typeSheet = new Worksheet($spreadsheet,$typeWording);
            $numberOfSheet = $spreadsheet->getSheetCount() - 1;
            $spreadsheet->addSheet($typeSheet, $numberOfSheet);

            $typeSheet = $spreadsheet->getSheet($numberOfSheet);

            $typeSheet->setCellValue('A1','code');
            $typeSheet->setCellValue('B1','name');
            $typeSheet->setCellValue('C1','order');
            $typeSheet->setCellValue('D1','parent');
            $typeSheet->setCellValue('E1','notes');

            
            $i = 2;
            foreach($realLevels as $level)
            {
                $typeSheet->setCellValue('A'.$i, $level->id);
                $typeSheet->setCellValue('B'.$i, $level->wording);
                $typeSheet->setCellValue('C'.$i, ($i-1));
                $typeSheet->setCellValue('D'.$i,'');
                $typeSheet->setCellValue('E'.$i,'');

                $i++;
            }

        }

        return $spreadsheet;
    }


    public function buildDataSheet($indicators, $types, Spreadsheet $spreadsheet)
    {
        $HLETTERS = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ');
        $lHIndex = 0;

        $unitPos = 0;
        $yearArray = [];
        $yearPosArray = [];

        $dataSheet = $spreadsheet->getActiveSheet();
        $dataSheet->setTitle("Donnees");
        
        $dataSheet->setCellValue($HLETTERS[0]."1","Indicateurs");
        $dataSheet->setCellValue($HLETTERS[1]."1","indicateursname");
        $lHIndex = 2;
        $typePosArray = [];
        $typeIdArray = [];
        $i = 2;
        $chkTypes = $types;

        foreach($types as $type)
        {
            $typeWording = Type::find($type)->wording;

            $dataSheet->setCellValue($HLETTERS[$lHIndex]."1",$typeWording);
            $typePosArray[] = $lHIndex; 
            $typeIdArray[] = $type;
            $lHIndex++;
            $dataSheet->setCellValue($HLETTERS[$lHIndex]."1",$typeWording."name");
            $lHIndex++;
        }


        $dataSheet->setCellValue($HLETTERS[$lHIndex]."1","Unit");

        $unitPos = $lHIndex;
        $lHIndex++;

        foreach($indicators as $indicator)
        {
            $indicatorI = Indicators::find($indicator);
            $datas = Data::where('id_indicator','=',$indicator)
                            ->orderBy('year','asc')
                            ->get();
            $levelArray = [];
            $levels = [];
            $types = [];
            $levelsName = [];
            $appendedLevelsArray = [];
            
            foreach($datas as $data) {
                $lvls = $data->levels;
                $levelsArr = [];
                $found = false;
                foreach($chkTypes as $type) {
                    $found = false;
                    foreach($lvls as $level) {
                        if($level->type->id == $type) {
                            $levelsArr[] = ['wording' => $level->wording, 'id' => $level->id, 'type_id' => $type];
                            $found = true;
                            continue;
                        }
                    }
                    if($found == false) {
                        $levelsArr[] = ['wording' => null, 'id' => null, 'type_id' => $type];
                    }
                }

                $data->levels = $levelsArr;
            }
            foreach($datas as $data)
            {
                $levelArray = $data->levels; 
                $appendedLevel = "";
                foreach($levelArray as $level)
                {
                    $typeId = $level['type_id'];
                    $types[] = $typeId;
                    $levels[] = $level['id'];
                    $levelsName[] = $level['wording'];
                    $appendedLevel .= $level['id'];
                }
                // dd($levels);
                if(array_key_exists($appendedLevel,$appendedLevelsArray) == true)
                {
                    $cLine = $appendedLevelsArray[$appendedLevel];

                    $dataSheet->setCellValue($HLETTERS[$unitPos].$cLine, "Nombre");

                    $year = $data->year;
                    $yearPos = null;
                    if(in_array($year, $yearArray))
                    {
                        $yearPos = array_search($year, $yearArray);
                    }
                    else
                    {
                        $yearArray[] = $year;
                        $yearPosArray[] = $lHIndex;
                        $lHIndex++;
                        $yearPos = array_search($year, $yearArray);
                    }
                    $yearIndex = $yearPosArray[$yearPos];
                    $dataSheet->setCellValue($HLETTERS[$yearIndex]."1", $data->year);
                    $dataSheet->setCellValue($HLETTERS[$yearIndex].$cLine, $data->value);

                }
                else
                {
                    $dataSheet->setCellValue($HLETTERS[0].$i, $indicatorI->id);
                    $dataSheet->setCellValue($HLETTERS[1].$i, $indicatorI->wording);

                    for($j = 0; $j < count($types); $j++)
                    {
                        $type = $types[$j];
                        $levelId = $levels[$j];
                        $levelName = $levelsName[$j];
                        $typePos = array_search($type,$typeIdArray);
                        $typePosIndex = $typePosArray[$typePos];

                        $dataSheet->setCellValue($HLETTERS[$typePosIndex].$i, $levelId);
                        $dataSheet->setCellValue($HLETTERS[$typePosIndex+1].$i, $levelName);
                    }

                    $dataSheet->setCellValue($HLETTERS[$unitPos].$i, "Nombre");

                    $year = $data->year;
                    $yearPos = null;
                    if(in_array($year, $yearArray))
                    {
                        $yearPos = array_search($year, $yearArray);
                    }
                    else
                    {
                        $yearArray[] = $year;
                        $yearPosArray[] = $lHIndex;
                        $lHIndex++;
                        $yearPos = array_search($year, $yearArray);
                    }
                    $yearIndex = $yearPosArray[$yearPos];
                    $dataSheet->setCellValue($HLETTERS[$yearIndex]."1", $data->year);
                    $dataSheet->setCellValue($HLETTERS[$yearIndex].$i, $data->value);
                    $appendedLevelsArray[$appendedLevel] = $i;
                    $i++;
                }

            }
            $appendedLevelsArray = [];
        }

        return $spreadsheet;
    }


    public function buildImportMask(Request $request)
    {
        $indicators = $request->input('indicators');
        $types = $request->input('types');
        $years = $request->input('years');

        $datasetname = $request->datasetname;
        $indicators = MiscController::chkArrayOfMultiSelect($indicators);
        $types = MiscController::chkArrayOfMultiSelect($types);
        $years = MiscController::chkArrayOfMultiSelect($years);
        $spreadsheet = new Spreadsheet();


        if(null != $indicators && null != $types)
        {
            $spreadsheet = $this->buildDatasetSheet($indicators, $datasetname, $types, $spreadsheet);
            $spreadsheet = $this->buildIndicatorsSheet($indicators, $spreadsheet);
            $spreadsheet = $this->buildTypesSheetForImport($types, $indicators, $spreadsheet);
            $spreadsheet = $this->buildImportMaskSheet($indicators, $types, $spreadsheet);

            $spreadsheet->setActiveSheetIndex(0);

            $writer = new Xlsx($spreadsheet);
            $path = public_path();
            $path = $path."/template/$datasetname.xlsx";
            $writer->save($path);

            return redirect()->back()->with("path","template/$datasetname.xlsx");
        }
    }

    public function buildImportMaskSheet($indicators, $types, Spreadsheet $spreadsheet)
    {
        $HLETTERS = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
        $lHIndex = 0;

        $unitPos = 0;
        $yearArray = [];
        $yearPosArray = [];

        $dataSheet = $spreadsheet->getActiveSheet();
        $dataSheet->setTitle("donnees");
        
        $dataSheet->setCellValue($HLETTERS[0]."1","Indicateurs");
        $dataSheet->setCellValue($HLETTERS[1]."1","indicateursname");
        $lHIndex = 2;
        $typePosArray = [];
        $typeIdArray = [];
        $lineArray = [];
        $i = 2;
        
        foreach($types as $type)
        {
            $typeWording = Type::find($type)->wording;

            $dataSheet->setCellValue($HLETTERS[$lHIndex]."1",$typeWording);
            $typePosArray[] = $lHIndex; 
            $typeIdArray[] = $type;
            $lHIndex++;
            $dataSheet->setCellValue($HLETTERS[$lHIndex]."1",$typeWording."name");
            $lHIndex++;
        }

        $dataSheet->setCellValue($HLETTERS[$lHIndex]."1","Unit");

        return $spreadsheet;
    }


    public function import(Request $request)
    {
        set_time_limit(360);
        $rules = array('importfile' => 'mimes:xlsx,xls');
 
        $file = $request->file('importfile');//->getClientOriginalExtension();
        $extension = $file->getClientOriginalExtension();
        $allowed = ['xlsx','xls'];
        //dd($file->getClientOriginalExtension());
        $validator = Validator::make($request->all(), $rules);
        $linesWithError = [];
        $linesError = [];

        if(!in_array($extension,$allowed))
        {
            dd('validation error');
            return redirect()->back()->with('validation','error');
        }
        else
        {
            //dd('test');
            $file = $request->file('importfile');//->getClientOriginalExtension();
            //dd($file->getClientOriginalExtension());
            $spreadsheet = IOFactory::load($file);

            //We access the last sheet of the excel file where we can find data to import
            $numberOfSheet = $spreadsheet->getSheetCount();
            $datasheet = $spreadsheet->getSheet(($numberOfSheet-1));

            $numberOfRow = $datasheet->getHighestDataRow();
  
            $highestColumn = $datasheet->getHighestDataColumn(); // e.g 'F'
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn); // e.g. 5
            $indicatorColumnIndex = 1;
            $unitPos = null;
            
            for($j = 1; $j <= $highestColumnIndex; $j++)
            {
                $unit = strtolower($datasheet->getCellByColumnAndRow($j,1));
                if($unit == 'unit')
                {
                    $unitPos = $j;
                    break;
                }
            }
            
            if($unitPos == null)
            {
                dd('unit error');
                return redirect()->back()->with('unit','unit');
            }

            for($i = 2; $i <= $numberOfRow; $i++)
            {
                $lineHasError = false;
                $levelsArray = [];
                $valueArray = [];
                $yearArray = [];
                $col = 1;
                $indicator = $datasheet->getCellByColumnAndRow($indicatorColumnIndex, $i)->getValue();
                if($indicator instanceof RichText)
                {
                    $indicator = (int)$indicator->getPlainText();
                }
                else
                {
                    $indicator = (int)$indicator;
                }
                
                if($indicator == null)
                {
                    $linesWithError[] = $i;
                    continue;
                }
                
                $linkedLevels = Indicators::find($indicator)->levels()->get();
                
                $rlinkedLevels = [];

                foreach($linkedLevels as $level)
                {
                    $rlinkedLevels[] = $level->id;
                }
                
                for($j = 1; $j <= $highestColumnIndex; $j++)
                {
                    $col = $col + 2;

                    if($col < $unitPos)
                    {
                        $levelId = $datasheet->getCellByColumnAndRow(($col),$i)->getValue();
                        
                        if($levelId instanceof RichText)
                        {
                            $levelId = (int)$levelId->getPlainText();
                        }
                        else
                        {
                            $levelId = (int)$levelId;
                        }
                        if(!in_array($levelId,$rlinkedLevels))
                        {
                            
                            $linesError[] = $i;
                            $lineHasError = true;
                            break;
                        }
                        else
                        {
                            $levelsArray[] = $levelId;
                        }
                        
                    }
                    
                    if($j > $unitPos)
                    {
                        $value = $datasheet->getCellByColumnAndRow(($j),$i)->getValue();
                        if($value instanceof RichText)
                        {
                            $value = (int)$value->getPlainText();
                        }
                        else
                        {
                            $value = (int)$value;
                        }

                        $year = (int)$datasheet->getCellByColumnAndRow(($j),1)->getValue();

                        if($value !== null && $value !== '0' && $year !== null)
                        {
                            $valueArray[] = $value;
                            $yearArray[] = $year;
                        }
                    }
                    

                }
                
                /* dd($levelsArray,$valueArray,$yearArray);
                if($levelsArray == null || (count($levelsArray))==0)
                {
                    if($lineHasError)
                    {
                        continue;
                    }
                    else
                    {
                        $linesError[] = $i;
                        continue;
                    }
                } */
                
                
                if($this->chkOldDataNotExist($indicator, $levelsArray, $yearArray) == false)
                {

                    $updated = $this->updateOldData($indicator, $levelsArray, $yearArray,$valueArray);
                    if($updated) continue;
                    if($lineHasError)
                    {
                        continue;
                    }
                    else
                    {
                        $linesError[] = $i;
                        continue;
                    }
                }
                
                $k = 0;
                foreach($valueArray as $value)
                {
                    $userId = Auth::user()->id;
                    $data = new Data;
                    //$data->id_level = $level; 
                    $data->id_indicator = $indicator; 
                    $data->id_user = $userId;
                    $data->value = $value;
                    $data->periodicity = null;
                    $data->year = $yearArray[$k];
                    $data->date_start = null;
                    $data->date_end =  null;
                    $data->observation = null;

                    if($data->save())
                    {
                        if($levelsArray!=null)
                        {
                            $data->levels()->attach($levelsArray);
                        }
                    }
                    else
                    {
                        continue;
                    }
                    
                    $k++;
                }
            }


            if($linesError == null || (count($linesError)==0))
            {
                return redirect()->back()->with('success','success');
            }
            else
            {
                $errorSpreadSheet = $this->copyRowsFromArray($linesError, $spreadsheet);

                $writer = new Xlsx($errorSpreadSheet);
                $path = public_path();
                $path = $path."/template/unsaved.xlsx";
                $writer->save($path);

                return redirect()->back()->with("path","template/unsaved.xlsx") 
                                        ->with('warning','warning');
            }
        }
    }

    public function updateOldData($indicator, $levelsArray, $yearArray,$valueArray)
    {
        $oldData = Data::where('id_indicator','=',$indicator)->get();
        foreach($oldData as $data)
        {
            $linkedLevels = $data->levels()->get();
            $rlinkedLevels = [];

            foreach($linkedLevels as $level)
            {
                $rlinkedLevels[] = $level->id;
            }

            if($this->arraysAreEquals($rlinkedLevels, $levelsArray))
            {
                if(in_array($data->year,$yearArray))
                {
                    $key = array_search($data->year,$yearArray);
                    $data->value = $valueArray[$key];
                    if($data->save()){
                        continue;
                    }else{
                        return false;
                    }
                    continue;
                }
                else
                {
                    continue;
                }
            }
            else
            {
                continue;
            }
        }

        return true;
    }

    public function chkOldDataNotExist($indicator, $levelsArray, $yearArray)
    {
        $oldData = Data::where('id_indicator','=',$indicator)->get();
        foreach($oldData as $data)
        {
            $linkedLevels = $data->levels()->get();
            $rlinkedLevels = [];

            foreach($linkedLevels as $level)
            {
                $rlinkedLevels[] = $level->id;
            }

            if($this->arraysAreEquals($rlinkedLevels, $levelsArray))
            {
                if(in_array($data->year,$yearArray))
                {
                    return false;
                }
                else
                {
                    continue;
                }
            }
            else
            {
                continue;
            }
        }

        return true;
    }

    public function arraysAreEquals($arrayOne, $arrayTwo)
    {
        $anElementIsNotFound = false;
        
        if(count($arrayOne)!=count($arrayTwo))
        {
            return false;
        }

        foreach($arrayOne as $arrayOneData)
        {
            if(!in_array($arrayOneData, $arrayTwo))
            {
               $anElementIsNotFound = true;
               break; 
            }
            else
            {
                continue;
            }
        }

        return (!$anElementIsNotFound);
    }

    public function copyRowsFromArray($rows, Spreadsheet $oldSpreadSheet)
    {
        $spreadsheet = new Spreadsheet();
        $numberOfSheet = $oldSpreadSheet->getSheetCount();
        $errorSheet = $spreadsheet->getActiveSheet();
        
        $oldDataSheet = $oldSpreadSheet->getSheet(($numberOfSheet-1));
        $highestColumn = $oldDataSheet->getHighestDataColumn(); // e.g 'F'
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn); // e.g. 5

        for($j = 1; $j <= $highestColumnIndex; $j++)
        {
            $oldCellValue = $oldDataSheet->getCellByColumnAndRow($j,1)->getValue();
            if($oldCellValue instanceof RichText)
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
            $errorSheet->getCellByColumnAndRow($j,1)->setValue($oldCellValue);
        }

        $numberOfRows = count($rows);
        $i = 0;
        for($row = 2; $row <= ($numberOfRows + 1); $row++)
        {
            for($j = 1; $j <= $highestColumnIndex; $j++)
            {
                $oldCellValue = $oldDataSheet->getCellByColumnAndRow($j,$rows[$i])->getValue();
                if($oldCellValue instanceof RichText)
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

    public static function chkUserStruct($level,$subdomainId)
    {
        $id_subdomain = $level->indicators->subdomain->id;

        if($subdomainId == $id_subdomain)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function deleteMultiple(Request $request) {
        $ids = $request->mdels;
        $ids = explode(";", $ids);
        Data::whereIn('id', $ids)->delete();
        return redirect()->back()->with('success','success');
       
    }

}

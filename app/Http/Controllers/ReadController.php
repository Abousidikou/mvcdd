<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Infos\InfosController;
use Illuminate\Http\Request;
use App\Models\Level;
use App\Models\Indicators;
use App\Models\Data;
use App\Models\Type;
use App\Models\Structures;
use App\Models\SubDomain;
use App\Models\Domain;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
 

class ReadController extends Controller
{
    //

    public function __construct()
    {
        //$this->middleware('auth');
    }
    
    public function readIndicators($id,$subdomain, Request $request)
    {
        $levelstf = $request->all();
        $levelToFilterWith = [];
        $periodes = [];
        foreach ($levelstf as $lev => $values) {
            if(is_array($values)){
                foreach ($values as $key => $value) {
                    if($value != 'null' && $value != null && $lev != 'filter' && $lev != 'plus') {
                        if(is_numeric($value) && strlen($value) == 4) {
                            $periodes[] = $value;
                            continue;
                        }
                        $levelToFilterWith[] = $value;
                    }
                }
            }
        }
        
        //dd($periodes,$levelToFilterWith);
        //dd($levelstf,$levelToFilterWith);
        $id_indicator = $id;
        $id_subdomain = $subdomain;
        $subdomain = SubDomain::find($id_subdomain);
        $indicator = Indicators::find($id_indicator);
        $id_domain = $subdomain->domain->id;
        session(['id_indicator' => $id_indicator]);
        session(['id_subdomain' => $id_subdomain]);
        session(['id_domain' => $id_domain]);
        session(['levelToFilterWith' => $levelToFilterWith]);

        /*   ------------------- start --------------------         */
        $plus = intval($request->input('plus','0'));
        $filter = intval($request->input('filter','10'));
        $last_id = ($filter == 0) ? 0 : $plus;
        //dd($filter,$plus);
        if(count($periodes) == 0){
            $total = Data::where('id_indicator',$id)->orderBy('year','desc')->orderBy('id','asc')->count();
        }else{
            $total = Data::where('id_indicator',$id)->whereIn('year',$periodes)->orderBy('year','desc')->orderBy('id','asc')->count();
        }
        //$c = 0;
        $off = 500;
        if(count($periodes) == 0){
            $infos = Data::where('id_indicator',$id)->orderBy('year','desc')->orderBy('id','asc')->offset($last_id * $off)->limit($off)->get();
        }else{
            $infos = Data::where('id_indicator',$id)->whereIn('year',$periodes)->orderBy('year','desc')->orderBy('id','asc')->offset($last_id * $off)->limit($off)->get();
        }
        
        $isPlus = (($total - ($last_id * $off) - $infos->count()) >  0) ? true : false;
        //dd($isPlus);

        /*   ------------------- end --------------------         */
        $infoArray = [];
        $types = [];
        $allyears = [];
        $allLevels = [];
        foreach($infos as $info)
        {
            $levels = $info->levels;
            //dd($levels);
            $levelsArr = [];
            foreach($levels as $level) {
                $type = $level->type->wording;
                if(in_array($type, $types)) {
                    continue;
                }
                $types[] = $type;
            }
        }
        //dd($types);
        foreach($infos as $info)
        {
            $levels = $info->levels;
            $levelsArr = [];
            $found = false;
            foreach($types as $type) {
                $found = false;
                foreach($levels as $level) {
                    if($level->type->wording == $type) {
                        $levelsArr[] = $level->wording;
                        $found = true;
                        continue;
                    }
                }
                if($found == false) {
                    $levelsArr[] = '--';
                }
            }

            if(in_array($info->year, $allyears)) {

            } else {
                $allyears[] = $info->year;
            }      

            $info->levels = $levelsArr;
            $info->lvhash = md5(serialize($levelsArr));
            $info->years = [];
        }

        /*foreach($infos as $info) {
            echo $info;
        }*/
        //dd($allyears);

        foreach($infos as $info) {
            if($levelToFilterWith != null && !empty($levelToFilterWith)) {
                if(count(array_intersect($levelToFilterWith, $info->levels)) != count($levelToFilterWith)) {
                    continue;
                }
            }
            //dd(array_intersect($levelToFilterWith, $info->levels),count($levelToFilterWith),$levelstf,$info->levels);
            if (array_key_exists($info->lvhash, $infoArray)) {
                $years = $infoArray[$info['lvhash']]['years'];
                $years[$info->year] = $info->value;
                $infoArray[$info['lvhash']]['years'] = $years;
            } else {
                $info['years'] = [$info->year => $info->value];
                $infoArray[$info['lvhash']] = $info;
            }
        }
        //dd($infoArray);
        $indicator = Indicators::where('id', $id_indicator)->first();
        $allLevels = $indicator->levels;
        $typeAndLevelArray = [];
        foreach($types as $type) {
            $typeAndLevelArray[$type] = [];
            foreach ($allLevels as $level) {
                if ($level->type->wording == $type) {
                    if(in_array($level->wording, $typeAndLevelArray[$type])) {
                        continue;
                    }
                    $typeAndLevelArray[$type][] = $level->wording;
                }
            }
        }
        
        $request->flash();
        $allyears = [];
        $allY = Data::select('year')->where('id_indicator',$id)->orderBy('year','desc')->orderBy('id','asc')->get();
        foreach ($allY as $key => $value) {
            $allyears[] = $value->year;
        }
        $allyears = array_unique($allyears);
        //dd($infoArray, $types, $allyears, $id_indicator,$id_subdomain, $subdomain, $indicator, $typeAndLevelArray);
        return view('read.index',compact('periodes','infoArray', 'types', 'allyears', 'id_indicator','id_subdomain', 'subdomain', 'indicator', 'typeAndLevelArray','last_id', 'isPlus'));
    }

    public function export($indicator,$subdomain)
    {
        // We retrieve the indicator id and the coresponding subdomain, then we try to get
        // all linked info and the type.
        $indicator_id = $indicator;
        $subdomain_id = $subdomain;
        $indicatorsArray = array($indicator_id);
        
        $allIndicators = [];
        $types = [];
        $allInfos = [];


        $indicators = Indicators::where('id',$indicator_id)->get();


        foreach($indicators as $indicator)
        {
            if($indicator->subdomain->id == $subdomain_id)
            {
                $allIndicators[] = $indicator;
            }
            else
            {
                continue;
            }
        }

        

        $infos = Data::where('id_indicator',$indicator_id)->get();

        foreach($infos as $info)
        {
            if($info->indicator->subdomain->id == $subdomain_id)
            {
                $allInfos[] = $info;
            }
            else
            {
                continue;
            }
        }



        foreach($allInfos as $info)
        {
            $levels = $info->levels()->get();
            foreach($levels as $level)
            {
                $typeId = $level->type->id;
                if(in_array($typeId,$types)==true)
                {
                    continue;
                }
                else
                {
                    $types[] = $level->type->id;
                }
            }
        }


        $infoController = new InfosController();

        $spreadsheet = new Spreadsheet();
        $datasetname = "info_exporte";
        if(null != $indicatorsArray && null != $types)
        {
            //We first build dataset worksheet;
            $spreadsheet = $infoController->buildDatasetSheet($indicatorsArray, $datasetname, $types, $spreadsheet);

            $spreadsheet = $infoController->buildIndicatorsSheet($indicatorsArray, $spreadsheet);
            $spreadsheet = $infoController->buildTypesSheet($types, $spreadsheet);
            $spreadsheet = $infoController->buildDataSheet($indicatorsArray, $types, $spreadsheet);

            $spreadsheet->setActiveSheetIndex(0);

            $writer = new Xlsx($spreadsheet);
            $path = public_path();
            $path = $path."/template/$datasetname.xlsx";
            $writer->save($path);
            return redirect()->back()->with("path","template/$datasetname.xlsx");
        }

    }

    public function viewChart($indicator,$subdomain, Request $request) { 
        $indicator = Indicators::find($indicator);
        $subdomain = SubDomain::find($subdomain);
        // $levelToFilterWith = session('levelToFilterWith', null);

        $id_domain = $subdomain->domain->id;
        session(['id_indicator' => $indicator->id]);
        session(['id_subdomain' => $subdomain->id]);
        session(['id_domain' => $id_domain]);
        // dd($id_domain);
        $infos = Data::where('id_indicator',$indicator->id)->orderBy('year','asc')->get();
        $infoArray = [];
        $types = [];
        $allyears = [];
        $chartData = [];
        foreach($infos as $info)
        {
            $levels = $info->levels;
            $levelsArr = [];
            foreach($levels as $level) {
                $levelsArr[] = $level->wording;
                $type = $level->type->wording;
                if(in_array($type, $types)) {
                    continue;
                }
                $types[] = $type;
            }

            if(in_array($info->year, $allyears)) {

            } else {
                $allyears[] = $info->year;
            }      

            $info->levels = $levelsArr;
            $info->lvhash = md5(serialize($levelsArr));
            $info->years = [];
        }

        foreach($infos as $info) {
            // if($levelToFilterWith != null && !empty($levelToFilterWith)) {
            //     if(count(array_intersect($levelToFilterWith, $info->levels)) != count($levelToFilterWith)) {
            //         continue;
            //     }
            // }
            if (array_key_exists($info->lvhash, $infoArray)) {
                $years = $infoArray[$info['lvhash']]['years'];
                $years[$info->year] = $info->value;
                $infoArray[$info['lvhash']]['years'] = $years;
            } else {
                $info['years'] = [$info->year => $info->value];
                $infoArray[$info['lvhash']] = $info;
            }
        }


        if($request->method() == 'POST') {
            $clevels = $request->clevels;
            /************* start  
             * 
             * My part 
             * 
             * ********************/
            if($request->clevels == null){
                return redirect($request->path())->with('status', 'Select!');
            }

            /******************/
            $years = $request->years;
            $infos = [];
            foreach($infoArray as $key => $info) {
                if(in_array($key, $clevels)) {
                    $infos[] = $info;
                }
            }

            $chartFirstLine = ['Année'];
            foreach($infos as $info) {
                $chartFirstLine[] = \implode(';', $info->levels);
            }
            $chartData[] = $chartFirstLine;

            foreach($allyears as $year) {
                $line = [$year];
                foreach($infos as $info) {
                    if(array_key_exists((int)$year, $info->years)) {
                        $line[] = (int)$info->years[(int)$year];
                    } else {
                        $line[] = 0;
                    }
                }
                $chartData[] = $line;
            }
            // dd($chartData);
            // return view('read.graph', compact('indicator', 'subdomain', 'allyears', 'infoArray', 'chartData'));
        }
        $chartData = json_encode($chartData);
        // dd($chartData);
        return view('read.graph', compact('indicator', 'subdomain', 'allyears', 'infoArray', 'chartData'));
    }

    public function array_key_first($a)
    {
        return array_keys($a)[0];
    }

}

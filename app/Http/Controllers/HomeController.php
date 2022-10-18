<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Data;
use App\Models\Domain;
use App\Models\Indicators;
use App\Models\Level;
use App\Models\Structures;
use App\Models\SubDomain;
use App\Models\Type;
use DateTime;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */ 
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $indicators = Indicators::all();
        $types = Type::all();
        $levels = Level::all();
        $subDomains = SubDomain::all();
        $data = Data::all();

        $numberOfIndicators = $indicators->count();
        $numberOfTypes = $types->count();
        $numberOfLevels = $levels->count();
        $numberOfData = $data->count();

        $returnedArray = $this->getNumberOfDataPerDomain($data);
        $domainArray = $returnedArray['dom'];
        $domainData = $returnedArray['dat'];
        $dataStatArray = [];
        $total = 0;

        $i = 0;
        foreach($domainArray as $dom)
        {
            $value = floor((($domainData[$i]/$data->count())*100));
            $dataStatArray[] = array('label' => $dom, 'value' => $value);
            $i++;
        }
        //dd($dataStatArray);
        $infos = Data::orderBy('id','desc')->limit(5)->get();
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

        
        return view('index',compact('numberOfIndicators','numberOfTypes','numberOfLevels','numberOfData','dataStatArray','infos'));
    }

    public function redirect() 
    {
        return redirect('/dashboard');
    }

    public function getNumberOfDataPerDomain($allData)
    {
        $domainArray = [];
        $domainData = [];
        foreach($allData as $data)
        {
            $domain = $data->indicator->subdomain->domain->wording;
            if(in_array($domain,$domainArray))
            {
                $index = array_search($domain, $domainArray);
                $domainData[$index] = $domainData[$index] + 1;
            }
            else
            {
                $domainArray[] = $domain;
                $domainData[] = 1;
            }

        }
        
        return array('dom' => $domainArray, 'dat' => $domainData);
    }

    public function stat(Request $request)
    {
        $data = Data::all();
        $returnedArray = $this->getNumberOfDataPerDomain($data);
        $domainArray = $returnedArray['dom'];
        $domainData = $returnedArray['dat'];
        $dataStatArray = [];
        $total = 0;

        $i = 0;
        foreach($domainArray as $dom)
        {
            $value = floor((($domainData[$i]/$data->count())*100));
            $dataStatArray[] = array('label' => $dom, 'value' => $value);
            $i++;
        }
        //dd($dataStatArray);
        return json_encode($dataStatArray);
    }


    public function auditANDtravail(){
        $dataToSend = [];
        $audit = $this->graphic(13,240,2012,2013,2014);
        $reform = $this->graphic(12,233,1397,903,904);
        
        $dataToSend[] = $audit;
        $dataToSend[] = $reform;

        
        return json_encode($dataToSend);
    }

    public function funcANDreform(){
        $dataToSend = [];
        $func = $this->graphic(11,211,1404,1405,1406);
        $trav = $this->graphic(10,191,739,738,1494);
        $dataToSend[] = $func;
        $dataToSend[] = $trav;
        
        return json_encode($dataToSend);
    }

    public function graphic($id_domain,$id_indicator,$id_first,$id_second,$id_third)
    {

        $allData = Data::all();


        $d1 = [];
        $d2 = [];
        $d3 = [];
        $dataToSend = [];
        $yearArray = [];
        $currrentYear = date('Y')+1;
        for ($x = 0; $x < 10; $x++) {
            $currrentYear = $currrentYear -1;
            $yearArray[] = $currrentYear;
            $d1[] = 0;
            $d2[] = 0;
            $d3[] = 0;
        } 

        
        //dd($yearArray,$dACE,$dAPE,$dTotal);
        foreach($allData as $data)
        {
            //dd($data->indicator->subdomain->domain->id == $id_domain);
            
            if($data->indicator->subdomain->domain->id == $id_domain){
                if($data->indicator->id == $id_indicator){
                    $year_id = array_search($data->year,$yearArray);
                    if($year_id != false){
                        //dd($year_id,$yearArray,$data->year,$data->value);
                        if(DB::table('data_levels')->where('id_data',$data->id)->where('id_level',$id_first)->first() != null){
                            $d1[$year_id] = $d1[$year_id] + (int)$data->value;
                        }elseif (DB::table('data_levels')->where('id_data',$data->id)->where('id_level',$id_second)->first() != null) {
                            $d2[$year_id] = $d2[$year_id] + (int)$data->value;
                        }elseif (DB::table('data_levels')->where('id_data',$data->id)->where('id_level',$id_third)->first() != null) {
                            $d3[$year_id] = $d3[$year_id] + (int)$data->value;
                        }
                        
                    }
                }
            }

        }

        /* $first = [];
        $second = [];
        $third = [];
        for ($x = 0; $x < 10; $x++) {
            $d = [];
            $d[] = $yearArray[$x];
            $d[] = $d1[$x];
            $first[] = $d;
            $d = [];
            $d[] = $yearArray[$x];
            $d[] = $d2[$x];
            $second[] = $d;
            $d = [];
            $d[] = $yearArray[$x];
            $d[] = $d3[$x];
            $third[] = $d;
        }  */
        $dataToSend[] = array_reverse($yearArray);
        $dataToSend[] = array_reverse($d1);
        $dataToSend[] = array_reverse($d2);
        $dataToSend[] = array_reverse($d3);
        return $dataToSend;
    }

    public function listSousdomain(){
        // Variables
        $id_func = 11;
        $id_trav = 10;
        $id_reform = 12;
        $id_audit = 13; 
        // get SubDomain 
        $dataToSend = [];
        $functionPublic = [];
        $travailSocial = [];
        $audit =    [];
        $reforme = [];

        // Getting though indicators
        $indicators = Indicators::all();
        foreach ($indicators as  $line) {

            if($line->subdomain->domain->id == $id_func){
                $dataExist = Data::where('id_indicator',$line->id)->first();
                if($dataExist != null){
                    if(array_key_exists($line->subdomain->wording,$functionPublic)){
                        $functionPublic[$line->subdomain->wording][0] += 1; 
                        $functionPublic[$line->subdomain->wording][1] += 1; 
                    }else{
                        $functionPublic[$line->subdomain->wording] = [1,1];
                    }
                }else{
                    if(array_key_exists($line->subdomain->wording,$functionPublic)){
                        $functionPublic[$line->subdomain->wording][1] += 1; 
                    }else{
                        $functionPublic[$line->subdomain->wording] = [0,1];
                    }
                }
            }elseif ($line->subdomain->domain->id == $id_trav){
                $dataExist = Data::where('id_indicator',$line->id)->first();
                if($dataExist != null){
                    if(array_key_exists($line->subdomain->wording,$travailSocial)){
                        $travailSocial[$line->subdomain->wording][0] += 1; 
                        $travailSocial[$line->subdomain->wording][1] += 1; 
                    }else{
                        $travailSocial[$line->subdomain->wording] = [1,1];
                    }
                }else{
                    if(array_key_exists($line->subdomain->wording,$travailSocial)){
                        $travailSocial[$line->subdomain->wording][1] += 1; 
                    }else{
                        $travailSocial[$line->subdomain->wording] = [0,1];
                    }
                }
            }elseif ($line->subdomain->domain->id == $id_audit) {
                $dataExist = Data::where('id_indicator',$line->id)->first();
                if($dataExist != null){
                    if(array_key_exists($line->subdomain->wording,$audit)){
                        $audit[$line->subdomain->wording][0] += 1; 
                        $audit[$line->subdomain->wording][1] += 1; 
                    }else{
                        $audit[$line->subdomain->wording] = [1,1];
                    }
                }else{
                    if(array_key_exists($line->subdomain->wording,$audit)){
                        $audit[$line->subdomain->wording][1] += 1; 
                    }else{
                        $audit[$line->subdomain->wording] = [0,1];
                    }
                }
            }elseif ($line->subdomain->domain->id == $id_reform) {
                $dataExist = Data::where('id_indicator',$line->id)->first();
                if($dataExist != null){
                    if(array_key_exists($line->subdomain->wording,$reforme)){
                        $reforme[$line->subdomain->wording][0] += 1; 
                        $reforme[$line->subdomain->wording][1] += 1; 
                    }else{
                        $reforme[$line->subdomain->wording] = [1,1];
                    }
                }else{
                    if(array_key_exists($line->subdomain->wording,$reforme)){
                        $reforme[$line->subdomain->wording][1] += 1; 
                    }else{
                        $reforme[$line->subdomain->wording] = [0,1];
                    }
                }
            }
        }

        $dataToSend[] = $travailSocial;
        $dataToSend[] = $functionPublic;
        $dataToSend[] = $reforme;
        $dataToSend[] = $audit;
        return json_encode($dataToSend);
    }
}

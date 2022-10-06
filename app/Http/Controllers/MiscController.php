<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Indicators;
use App\Models\Structures;
use App\Models\SubDomain;
use App\Models\Domain;
use App\Models\Data;

class MiscController extends Controller
{
    //
 
    public static function buildReadUI($firstId = 0)
    {
        $domains = Domain::all();

        $builtUI = "<ul class='demo-choose-skin'>";

        foreach($domains as $domain)
        {
            if($firstId == 0)
            {
                session(['id_domain' => $domain->id]);
                // $firstId = $domain->id;
                $builtUI .= "<li class='active struct' id=".$domain->id."><a href='#'>$domain->wording</a></li>"; 
                continue;
            }
            else if($firstId = $domain->id)
            {
                $builtUI .= "<li class='active struct' id=".$domain->id."><a href='#'>$domain->wording</a></li>"; 
                continue;
            }
            $builtUI .= "<li class='struct' id=".$domain->id."><a href='#'>$domain->wording</a></li>";    
        }

        $builtUI .="</ul>";

        return array('builtUI' => $builtUI);
    }
 
    public static function buildReadUITwo($firstId = 0)
    {
        $domains = Domain::all();

        $builtUI = "";
        foreach($domains as $domain)
        {
            if($firstId == 0)
            {
                session(['id_domain' => $domain->id]);
                // $firstId = $domain->id;
                $builtUI .= "<a class='active struct btn btn-default hbar' data-id=".$domain->id." href='#'>$domain->wording</a>&nbsp;"; 
                continue;
            }
            else if($firstId == $domain->id)
            {
                // $firstId = $domain->id;
                $builtUI .= "<a class='active struct btn btn-default hbar' data-id=".$domain->id." href='#'>$domain->wording</a>&nbsp;"; 
                continue;
            }
            $builtUI .= "<a class='active struct btn btn-primary hbar' data-id=".$domain->id." href='#'>$domain->wording</a>&nbsp;";    
        }

        return array('builtUI' => $builtUI);
    }

    public static function buildISUI($id=1)
    {
        $subdomains = SubDomain::where('id_domain','=',$id)->get();
        $builtSUI = "";
        // dd($id);
        $active_subdomain = session('id_subdomain');
        $active_indicator = session('id_indicator');

        foreach($subdomains as $subdomain)
        {
            $indicators = Indicators::where('id_subdomain',$subdomain->id)->get();
            if($indicators->isEmpty())
            {
                continue;
            }
            if ($active_subdomain == $subdomain->id)
            {
                $builtSUI .= "<li class='subdomains active' id='s$subdomain->id'>
                <a href='javascript:void(0);' class='menu-toggle'>
                    <i class='material-icons'>swap_calls</i>
                    <span>$subdomain->wording</span>
                </a> 
                ";
            }
            else
            {
                $builtSUI .= "<li class='subdomains' id='s$subdomain->id'>
                <a href='javascript:void(0);' class='menu-toggle'>
                    <i class='material-icons'>swap_calls</i>
                    <span>$subdomain->wording</span>
                </a> 
                ";
            }

            $i = 0; 
            foreach($indicators as $indicator)
            {
                //dd($indicator);
                $isDataExist = Data::where('id_indicator',$indicator->id)->first();
                if($isDataExist != null){
                    $i++;
                    if($i == 1 && ($active_indicator == $indicator->id))
                    {
                        $builtSUI .= "<ul class='ml-menu'><li class='indicators active' id='i$indicator->id'><a href=".url('/read/'.$indicator->id.'/'.$subdomain->id)."><i class='material-icons'>layers</i><span>$indicator->wording</span></a></li>";
                        continue;
                    }
                    else if($i == 1)
                    {
                        $builtSUI .= "<ul class='ml-menu'><li class='indicators' id='i$indicator->id'><a href=".url('/read/'.$indicator->id.'/'.$subdomain->id)."><i class='material-icons'>layers</i><span>$indicator->wording</span></a></li>";
                        continue;
                    }
                    else if($i != 1 && ($active_indicator == $indicator->id))
                    {
                        $builtSUI .= "<li class='indicators active' id='i$indicator->id'><a href=".url('/read/'.$indicator->id.'/'.$subdomain->id)."><i class='material-icons'>layers</i><span>$indicator->wording</span></a></li>";                    
                    }
                    else
                    {
                        $builtSUI .= "<li class='indicators' id='i$indicator->id'><a href=".url('/read/'.$indicator->id.'/'.$subdomain->id)."><i class='material-icons'>layers</i><span>$indicator->wording</span></a></li>";
                    }
                }

            }
            $builtSUI .= "</ul>";
        }

        $builtSUI .= "</li>";
        return $builtSUI;
    }

    public static function chkArrayOfMultiSelect($multiselect)
    {
        if($multiselect==null)
        {
            return null;
        }
        else if(count($multiselect) > 1)
        {
            if(in_array("null",$multiselect))
            {
                $realMultiselect = [];
                unset($multiselect[0]);
                foreach($multiselect as $selected)
                {
                    $realMultiselect [] = $selected;
                }
                return $realMultiselect;
            }
            else
            {
                return $multiselect;
            }
        }
        else if(count($multiselect) == 1)
        {
            if(in_array("null",$multiselect))
            {
                return null;
            }
            else
            {
                return $multiselect;
            }
        }
    }

    public static function toMysqlDate($sDate)
    {
        $sDateExplodedArray = explode('/',$sDate);
        $mySqlDate = $sDateExplodedArray[2]."-".$sDateExplodedArray[1]."-".$sDateExplodedArray[0];
        return $mySqlDate;
    }

    public function setBuiltSUI(Request $request)
    {
        if($request->ajax())
        {
            $domainId = $request->id;
            session(['id_domain' => $domainId]);
            $builtSUI = MiscController::buildISUI($domainId);

            echo $builtSUI;
        }
    }


    public function getSubdomains(Request $request)
    {
        if($request->ajax())
        {
            $domainId = $request->id;

            $subdomains = SubDomain::where('id_domain',$domainId)->get();
            $subdomainsOptions = "<option value='null'>Veuillez choisir le sous domaine</option>";

            foreach($subdomains as $subdomain)
            {
                $subdomainsOptions .= "<option value=$subdomain->id>$subdomain->wording</option>";
            }

            echo $subdomainsOptions;

        }
    }

    public function getIndicators(Request $request)
    {
        if($request->ajax())
        {
            $subdomainId = $request->id;

            $indicators = Indicators::where('id_subdomain',$subdomainId)->get();
            $indicatorsOptions = "<option value='null'>Veuillez choisir un indicateur</option>";

            foreach($indicators as $indicator)
            {
                $indicatorsOptions .= "<option value=$indicator->id>$indicator->wording</option>";
            }

            echo $indicatorsOptions;

        }
    }

    public function getLevels($infoId=null, Request $request)
    {
        if($request->ajax())
        {
            $indicatorId = $request->id;

            $levels = Indicators::find($indicatorId)->levels()->get();
           
            $levelsOptions = "<option value='null'>Veuillez choisir un ou des niveaux</option>";

            $levelArray = [];
            if ($infoId != null) {
                $info = Data::where('id',$infoId)->first();
                $selectedLevels = $info->levels;

                foreach($selectedLevels as $slevel) {
                    $levelArray[] = $slevel->id;
                }
            }

            $allTypes = [];
            foreach($levels as $level)
            {
                $type = $level->type->wording;
                if (in_array($type, $allTypes)) {
                    continue;
                }
                $allTypes[] = $type;
            }

            $opts = "";

            foreach($allTypes as $type) {
                $opts = "<optgroup label=$type>";
                foreach ($levels as $level) {
                    if ($level->type->wording == $type) {
                        if(in_array($level->id, $levelArray)) {
                            $opts .= "<option value=$level->id selected>$level->wording</option>";
                            continue;
                        }
                        $opts .= "<option value=$level->id>$level->wording</option>";
                    }
                }

                $opts .= "</optgroup>";
                $levelsOptions .= $opts;
            }

            echo $levelsOptions;
        }
    }

    public function getTypes(Request $request)
    {
        if($request->ajax())
        {
            $indicators = $request->input('indicators');

            $indicatorArray = explode(',',$indicators);
            $typesOptions = "<option value='null'>Veuillez choisir des types de niveau</option>";
            $typeArray = [];
            foreach($indicatorArray as $indicatorId)
            {
                if($indicatorId == 'null')
                {
                    continue;
                }

                $levels = Indicators::find($indicatorId)->levels()->get();
            
                foreach($levels as $level)
                { 
                    
                    if(in_array($level->id_type,$typeArray))
                    {
                        continue;
                    }
                    else
                    { 
                        $typeId = $level->type->id;
                        $typeWording = $level->type->wording;
                        $typesOptions .= "<option value=$typeId>$typeWording</option>";
                        $typeArray[] = $level->id_type;
                    }
                }
            }
            
            echo $typesOptions;
        }
    }
}

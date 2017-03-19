<?php

namespace App\Http\Controllers\Client\Get;
use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Controller;
use URL;
use Redirect;

use App\Http\Controllers\ErrorController;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use Dingo\Api\Routing\Helpers;

use App\User;
use App\Role;

class SponsorshipController extends Controller
{

    public function getAllSponsors(){

        $sponsors = DB::table('users')
            ->join('sponsors','users.id','=','sponsors.id')
            ->where('reviewedByAdmin','=',1)
            ->where('active',1)
            ->get();
         
        $sponsors = json_encode($sponsors);
        $sponsors = json_decode($sponsors,true);
         
        foreach ($sponsors as $key => $value) {
            $currentMembership=DB::table('payments')
                ->where('payments.user_id',$sponsors[$key]['id'])
                ->where('payments.active',1)
                ->join('products','products.id','=','payments.item_number')
                //->select('products.name')
                ->first();
            $sponsors[$key]['membership_type']=$currentMembership->item_number;
            $sponsors[$key]['membership_type_name']=$currentMembership->name;
            $sponsors[$key]['sponsor_id']=intval($sponsors[$key]['id']);
            $sponsors[$key]['sponsor_name']=$sponsors[$key]['name'];
            $sponsors[$key]['image']=$sponsors[$key]['profile_picture'];
        }        
        
        $sponsorsList=array();
        $key1=0;
        foreach ($sponsors as $key => $value) {
            $sponsorsList[$key1]['sponsor_id']=$sponsors[$key]['sponsor_id'];
            $sponsorsList[$key1]['sponsor_name']=$sponsors[$key]['sponsor_name'];
            $sponsorsList[$key1]['email']=$sponsors[$key]['email'];
            $sponsorsList[$key1]['image']=$sponsors[$key]['image'];
            $sponsorsList[$key1]['link']=$sponsors[$key]['link'];
            $sponsorsList[$key1]['membership_type']=intval($sponsors[$key]['membership_type']);
            $sponsorsList[$key1]['membership_type_name']=$sponsors[$key]['membership_type_name'];
        }        


        $sponsorsList = json_encode($sponsorsList);
        $sponsorsList = str_replace("\\", "", $sponsorsList);
        
        $message='Done';
        $codex=1;
        $status_code=200;
        $data=$sponsorsList;
        //$data=$categories;
        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
        return $Respose;

    }

}

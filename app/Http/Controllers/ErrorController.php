<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Carbon\Carbon;

class ErrorController extends Controller
{
    public static function error307(){
        $message='User is currently not active, or user might be blocked by the admin.';
        $codex=0;
        $status_code=307;
        $data='""';
        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
        return $Respose;
    }

    public static function error401(){
        $message='Token problem! ';
        $codex=0;
        $status_code=401;
        $data='""';
        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
        return $Respose;
    }

    public static function error500(){
        $message='Unknown error!';
        $codex=0;
        $status_code=500;
        $data='""';
        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
        return $Respose;
    }

    public static function error405(){
        $message='Action not allowed!';
        $codex=0;
        $status_code=405;
        $data="";
        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
        return $Respose;
    }


}

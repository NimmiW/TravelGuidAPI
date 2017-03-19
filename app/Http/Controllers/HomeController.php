<?php

namespace App\Http\Controllers;
//require 'vendor/autoload.php';
use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index(){
    	
        echo Carbon::now();
 		//$users=User::all();
 		//print_r($users);
    }

    public function hello(){
    	$user = JWTAuth::parseToken()->authenticate();
    	if(!$user){
    		return "Error";
    	}
    	else{
    		if (\Entrust::hasRole('traveler')){
    			echo "traveler";
    		}else{
    			echo "Not a traveler";
    		}
    		if(\Entrust::can('add-admins')){
    			echo "i can add a admin";
    		}else{
    			echo "i can not add a admin";
    		}
    	}
    	return $user->id;
    }
}

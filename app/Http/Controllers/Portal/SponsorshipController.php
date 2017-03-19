<?php

namespace App\Http\Controllers\Portal;
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

    public function getSponsorship(){
        $sponsorships = DB::table('products')->get();
        $sponsorships = json_decode(json_encode($sponsorships), True);
        //return $view = view('payments.sponsorships')->with('sponsorships', $sponsorships);
        return json_encode($sponsorships);
    }

    public function success(Request $request){

        //Store transaction information from PayPal
        $item_number = $request->input('item_number'); 
        $txn_id = $request->input('tx');
        $payment_gross = $request->input('amt');
        $currency_code = $request->input('cc');
        $payment_status = $request->input('st');
        $user_id = 6;//get this value from session

        //Get product price
        $productResult = DB::table('products')
                            ->where('id',$item_number)
                            ->first();
        $productResult = json_decode(json_encode($productResult), True);
        $productPrice = $productResult['price'];

        if(!empty($txn_id) && $payment_gross == $productPrice){

            
            $id = DB::table('payments')->insertGetId(
                [
                    'item_number' => $item_number, 
                    'txn_id' => $txn_id,
                    'payment_gross' => $payment_gross,
                    'currency_code' => $currency_code,
                    'payment_status' => $payment_status,
                    'user_id' => $user_id
                ]
            );

            $message='Done!';
            $codex=1;
            $status_code=200;
            $data='{"transaction_reference_number":'.$id.'}';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;

        }else{
            $message='Unknown error!';
            $codex=0;
            $status_code=416;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }
    }

    public function attachSponsorRole(Request $request){
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }
            
            if (\Entrust::hasRole('business-user')){
                
                $sponsor=Role::find(6);
                $user->attachRole($sponsor);
                DB::table('sponsors')->insert(
                    ['id' => $user->id, 'link' => $request->input('link')]
                );

                DB::table('users')
                ->where('id',$user->id)
                ->update(
                    ['profile_picture' => $request->input('profile_picture')]
                );

                $message='Done!';
                $codex=1;
                $status_code=200;
                $data=json_encode($user);
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :  '.$data.'}';
                return $Respose;
                        
            }else{
                return ErrorController::error405();
            }

        }catch(JWTException $ex){
            return ErrorController::error401();
        }catch(\Exception $ex){
            return ErrorController::error500();
        }        
    }

    public function updateMySponserDetails(Request $request){
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }
            
            if (\Entrust::hasRole('sponsor')){
                
                
                DB::table('sponsors')
                ->where('id',$user->id)
                ->update(
                    ['link' => $request->input('link')]
                );

                DB::table('users')
                ->where('id',$user->id)
                ->update(
                    ['profile_picture' => $request->input('profile_picture')]
                );

                $message='Done!';
                $codex=1;
                $status_code=200;
                $data='""';
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :  '.$data.'}';
                return $Respose;
                        
            }else{
                return ErrorController::error405();
            }

        }catch(JWTException $ex){
            return ErrorController::error401();
        }catch(\Exception $ex){
            return ErrorController::error500();
        }         
    }

    public function getMySponserDetails(){
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }
            
            if (\Entrust::hasRole('sponsor')){
                
                
                $sponsorship=DB::table('sponsors')
                ->where('id',$user->id)
                ->first();

                $payments=DB::table('payments')
                ->where('user_id',$user->id)
                ->get();

                $currentMembership=DB::table('payments')
                ->where('payments.user_id',$user->id)
                ->where('payments.active',1)
                ->join('products','products.id','=','payments.item_number')
                ->first();

                $message='Done!';
                $codex=1;
                $status_code=200;
                $data='{"sponsorship":'.json_encode($sponsorship).',"payments":'.json_encode($payments).',"currentMembership":"'.$currentMembership->name.'"}';
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :  '.$data.'}';
                return $Respose;
                        
            }else{
                return ErrorController::error405();
            }

        }catch(JWTException $ex){
            return ErrorController::error401();
        }catch(\Exception $ex){
            return ErrorController::error500();
        }         
    }

}

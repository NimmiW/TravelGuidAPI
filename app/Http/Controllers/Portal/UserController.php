<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Permission;
use App\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\ErrorController;
use DB;
use App\Role;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function attachUserRole($userId,$roleName){//not working
    	$user = User::find($userId);

    	$roleId = Role::where('name',$roleName)->first();

    	$user->roles->attach($roleId);

    	print_r($user) ;
    }

    public function getUserRole(Request $request){//check roles of a particular user
    	$user_id=$request->input('user_id');

        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }
            
            if (\Entrust::hasRole('admin')){
                
                $permissionArray=array();
                $key1=0;
                $user_roles=User::find($user_id)->roles;
                foreach ($user_roles as $key => $value) {
                    $permissions=DB::table('permission_role')
                    ->join('roles','roles.id','=','permission_role.role_id')
                    ->where('roles.id',$value->id)//->permissions;
                    ->select('permission_role.permission_id')
                    ->get();
                    
                    $permissions = json_decode(json_encode($permissions), True);

                    foreach ($permissions as $key => $value) {
                        $permission_name=DB::table('permissions')
                        ->where('id',$value['permission_id'])//->permissions;
                        ->first()->name;
                        $permissions[$key]['permission_name']=$permission_name;

                    }
                    $permissionArray[$key1]=$permissions;
                    $key1++;
                }
                

                $message='Done!';
                $codex=1;
                $status_code=200;
                $data=json_encode($permissionArray);
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :  '.$data.'}';
                return $data;
                        
            }else{
                return ErrorController::error405();
            }

        }catch(JWTException $ex){
            return ErrorController::error401();
        }catch(\Exception $ex){
            return ErrorController::error500();
        }

    
    }


    public function getAllUsers(){//get a list of users without roles
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return "5";//invalid token
            }
            
            if (\Entrust::hasRole('admin')){
                
                
                $users=User::all();
                $user= json_encode($users);
                $message='Done!';
                $codex=1;
                $status_code=200;
                $data=$user;
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :  '.$data.'}';
                return $Respose;
                        
            }else{
                $message='Action not allowed!';
                $codex=0;
                $status_code=405;
                $data="";
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                return $Respose;
            }

        }catch(JWTException $ex){
                $message='Token problem!';
                $codex=0;
                $status_code=401;
                $data="";
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                return $Respose;
        }catch(\Exception $ex){
            $message='Unown Error!';
            $codex=0;
            $status_code=500;
            $data="";
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
            return $Respose;
        }

    }


    public function getAllBusinessUsers(){

        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }
            
            if (\Entrust::hasRole('admin')){
                
                $BU=DB::table('role_user')
                    ->join('users','users.id','=','role_user.user_id')
                    ->where('role_user.role_id',3)
                    ->where('users.active',1)
                    ->where('users.reviewedByAdmin',1)
                    ->select(
                        'users.id',
                        'users.name',
                        'sur_name',
                        'email',
                        'date_of_birth',
                        'telephone',
                        'country',
                        'is_online'

                        )
                    ->get();

                $BU = json_decode(json_encode($BU), True);
                foreach ($BU as $key => $value) {
                    $guideRole=DB::table('role_user')
                        ->where('user_id',$BU[$key]['id'])
                        ->where('role_id',4)
                        ->first();
                    if($guideRole){
                        $BU[$key]['is_guide']=1;
                        $guideDetails=DB::table('guides')
                                        ->where('id',$BU[$key]['id'])
                                        ->first();
                        $BU[$key]['gov_registration_id']=$guideDetails->gov_registration_id;
                        $BU[$key]['address']=$guideDetails->address;
                        $BU[$key]['available_areas']=$guideDetails->available_areas;
                        $BU[$key]['languages']=$guideDetails->languages;

                    }else{
                        $BU[$key]['is_guide']=0;
                    }

                    $sponsorRole=DB::table('role_user')
                        ->where('user_id',$BU[$key]['id'])
                        ->where('role_id',6)
                        ->first();
                    if($sponsorRole){
                        $BU[$key]['is_sponsor']=1;

                    }else{
                        $BU[$key]['is_sponsor']=0;
                    }
                }
             
                

                $message='Done!';
                $codex=1;
                $status_code=200;
                $data=json_encode($BU);
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
    public function getAllAdmins(){

        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }
            
            if (\Entrust::hasRole('admin')){
                
                $admins=DB::table('users')
                    ->join('role_user','users.id','=','role_user.user_id')
                    ->join('roles','role_user.role_id','=','roles.id')
                    ->where('role_user.role_id','=',2)
                    ->select(
                        'users.id',
                        'users.name',
                        'sur_name',
                        'email',
                        'date_of_birth',
                        'telephone',
                        'country',
                        'is_online'

                        )
                    ->get();
             
                

                $message='Done!';
                $codex=1;
                $status_code=200;
                $data=json_encode($admins);
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


    public function getNotReviewedBU(){


        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }
            
            if (\Entrust::hasRole('admin')){

                $BU = DB::table('users')
                    ->join('role_user','users.id','=','role_user.user_id')
                    ->where('role_user.role_id',3)
                    ->where('users.active',1)
                    ->where('users.reviewedByAdmin',0)
                    ->select(
                        'users.id',
                        'users.name',
                        'sur_name',
                        'email',
                        'date_of_birth',
                        'telephone',
                        'country',
                        'is_online'
                        )
                    ->get();

                $BU = json_decode(json_encode($BU), True);
                foreach ($BU as $key => $value) {
                    $guideRole=DB::table('role_user')
                        ->where('user_id',$BU[$key]['id'])
                        ->where('role_id',4)
                        ->first();
                    
                    if($guideRole){
                        $BU[$key]['is_guide']=1;
                        $guideDetails=DB::table('guides')
                                        ->where('id',$BU[$key]['id'])
                                        ->first();
                        $guideDetails = json_decode(json_encode($guideDetails), True);
                        $BU[$key]['gov_registration_id']=$guideDetails['gov_registration_id'];
                        $BU[$key]['address']=$guideDetails['address'];
                        $BU[$key]['available_areas']=$guideDetails['available_areas'];
                        $BU[$key]['languages']=$guideDetails['languages'];

                    }else{
                        $BU[$key]['is_guide']=0;
                    }

                    $sponsorRole=DB::table('role_user')
                        ->where('user_id',$BU[$key]['id'])
                        ->where('role_id',6)
                        ->first();
                    if($sponsorRole){
                        $BU[$key]['is_sponsor']=1;

                    }else{
                        $BU[$key]['is_sponsor']=0;
                    }
                }
             

                $BU = json_encode($BU);
                $BU = str_replace("\\", "", $BU);
                
                $message='Done!';
                $codex=1;
                $status_code=200;
                $data=$BU;
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
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

    public function getAllTravelers(){
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }
            
            if (\Entrust::hasRole('admin')){

                $travelers = DB::table('users')
                    ->join('role_user','users.id','=','role_user.user_id')
                    ->where('role_user.role_id',5)
                    ->where('users.active',1)
                    ->select(
                        'users.id',
                        'users.name',
                        'sur_name',
                        'email',
                        'date_of_birth',
                        'telephone',
                        'country',
                        'is_online'
                        )
                    ->get();


             

                $travelers = json_encode($travelers);
                $travelers = str_replace("\\", "", $travelers);
                
                $message='Done!';
                $codex=1;
                $status_code=200;
                $data=$travelers;
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
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

    public function addAdmin(Request $request){
        try{

            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }

            if (\Entrust::hasRole('super-admin')){
                try{
                    $email=DB::table('users')
                    ->where('email','=',$request->input('email'))->first();
                    if($email){
                        $message='User already exists!';
                        $codex=0;
                        $status_code=416;
                        $data='""';
                        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                        return $Respose;
                    }
   

                    $newUser = new User();
                    $newUser->name=$request->input('name');
                    $newUser->email=$request->input('email');
                    $newUser->password=Hash::make($request->input('password'));
                    $newUser->sur_name=$request->input('sur_name');
                    $newUser->country=$request->input('country');
                    $newUser->telephone=$request->input('telephone');
                    $newUser->date_of_birth=$request->input('date_of_birth');
                    $newUser->reviewedByAdmin=1;

                    
                    if($newUser->save()){
                        $admin=Role::find(2);
                        $newUser->attachRole($admin);

                        $message='Admin was Successfully registered in the system!';
                        $codex=1;
                        $status_code=200;
                        $data='""';
                        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                        return $Respose;
                        
                    }else{ 
                        $message='Internal server error!';
                        $codex=0;
                        $status_code=500;
                        $data='""';
                        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                        return $Respose;
                    }
               
                }catch(\Exception $ex){
                    return ErrorController::error500();
                }
            }else{
                return ErrorController::error405();
            }
        }catch(JWTException $ex){
            return ErrorController::error401();
        }catch(\Exception $ex){
            return ErrorController::error500();
        }
    }

    public function blockUser(Request $request){
        try{

            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }

            if (\Entrust::hasRole('super-admin')){
                
                $user_id=$request->input('user_id'); 

                $user = DB::table('users')
                ->where('id', $user_id)
                ->update(['active' => 0]);
                if($user==0){
                    $message='User was already blocked!';//user is already removed by the super admin
                    $codex=0;
                    $status_code=404;
                    $data="";
                    $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                    return $Respose;
                }
                else {
                    $message='User was successfully blocked!';
                    $codex=1;
                    $status_code=200;
                    $data="";
                    $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                    return $Respose;
                }
            }else{
                return ErrorController::error405();
            }
        }catch(JWTException $ex){
            return ErrorController::error401();
        }catch(\Exception $ex){
            return ErrorController::error500();
        }
    }

    public function setReviewedByAdminStatusToBU(Request $request){
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }
            
            if (\Entrust::hasRole('admin')){

                $user_id=$request->input('user_id');
                $user_accept_decline_status=$request->input('user_accept_decline_status');

                if($user_accept_decline_status==1){
                    $user=User::where('id',$user_id)->update(['reviewedByAdmin'=>1,'active'=>1]);
                    $message='The user was accepted!';
                    $codex=1;
                    $status_code=200;
                    $data='""';
                    $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" : '.$data.'}';
                    return $Respose;
                }else{
                    $user=User::where('id',$user_id)->update(['reviewedByAdmin'=>1,'active'=>0]);
                    $message='The user was rejected!';
                    $codex=1;
                    $status_code=200;
                    $data='""';
                    $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                    return $Respose;
                }

                
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

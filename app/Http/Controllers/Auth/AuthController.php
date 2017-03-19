<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Role;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use Dingo\Api\Routing\Helpers;
use Hash;
use DB;
use Crypt;
//use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins,Helpers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    /*public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }*/

    public function authenticate(Request $request){
        
        $credentials = $request->only('email','password');

        $email=$request->email;
        $userx=User::where('email',$email)->first();

        if($userx){
            if($userx->active==0){
                $message='User is currently not active, or user might be blocked by the admin.';
                $codex=0;
                $status_code=307;
                $data='""';
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                return $Respose;
            }               
        }     
        try{
            if(!$user=JWTAuth::attempt($credentials)){

                $message='User not found. Invalid login details!';
                $codex=0;
                $status_code=404;
                $data='""';
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                return $Respose;
            }
 

        }catch(JWTException $ex){
            $message='Token problem! ';
            $codex=0;
            $status_code=401;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;

        }catch(\Exception $ex){
            $message='Unknown error!';
            $codex=0;
            $status_code=500;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }

        $message='Done!';
        $codex=1;
        $status_code=200;
        $data=json_encode(compact('user'));
        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
        return $Respose;
        
    }

    public function authenticateForPortal(Request $request){
        
        $credentials = $request->only('email','password');

        $email=$request->email;
        $userx=User::where('email',$email)->first();

        if($userx){
            if($userx->active==0){
                $message='User is currently not active, or user might be blocked by the admin.';
                $codex=0;
                $status_code=307;
                $data='""';
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                return $Respose;
            }               
        }     
        try{
            if(!$user=JWTAuth::attempt($credentials)){

                $message='User not found. Invalid login details!';
                $codex=0;
                $status_code=404;
                $data='""';
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                return $Respose;
            }
 

        }catch(JWTException $ex){
            $message='Token problem! ';
            $codex=0;
            $status_code=401;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;

        }catch(\Exception $ex){
            $message='Unknown error!';
            $codex=0;
            $status_code=500;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }

        if(\Entrust::hasRole('traveler')){
            $message='Sorry you are a trvaler, Forbidden to login to Admin Portal!';
            $codex=0;
            $status_code=403;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }

        if(\Entrust::hasRole('sponsor')){
            $currentMembership=DB::table('payments')
                ->where('payments.user_id',$userx->id)
                ->join('products','products.id','=','payments.item_number')
                ->select('payments.active','products.name')
                ->first();
            $currentMembership=json_decode(json_encode($currentMembership),true);
            if(intval($currentMembership['active'])==1){
                $active=1;
                $message='Done!';
                $codex=1;
                $status_code=200;
                $data='{"user":"'.$user.'","active":1,"currentMembership":"'.$currentMembership['name'].'"}';
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                
                return $Respose;
            }else{
                $active=1;
                $message='Not paid!';
                $codex=1;
                $status_code=200;
                $data='{"user":"'.$user.'","active":0}';
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                return $Respose;
            }
        }

        $message='Done!';
        $codex=1;
        $status_code=200;
        $data=json_encode(compact('user'));
        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
        return $Respose;
        
    }


    public function registerApp(Request $request){
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
                //return '6';

            $newUser = new User();
            $newUser->name=$request->input('name');
            $newUser->email=$request->input('email');
            $newUser->password=Hash::make($request->input('password'));
            $newUser->sur_name=$request->input('sur_name');
            $newUser->country=$request->input('country');
            $newUser->telephone=$request->input('telephone');
            $newUser->date_of_birth=$request->input('date_of_birth');

            $token=str_shuffle(\Carbon\Carbon::now()."slohoneyarkoo".$newUser->email."crooogecbdhhd");//randomly picked
            $encrypted = Crypt::encrypt($token);

            $newUser->remember_token=$token;
            
            if($newUser->save()){
                $traveler=Role::find(5);
                $newUser->attachRole($traveler);

                /*Mail::send('emails.verifyRegistration', ['name' => $newUser->name,'token'=>$token,'email'=>$newUser->email], function ($m) use ($newUser) {
                    $m->to($newUser->email, $newUser->name)->subject('SLTravelmate | Verify the email to complete registration');
                });*/

                $message='Successfully registered! Please go to e-mails and verify your e-mail address.';
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
            $message='Unknown error!';
            $codex=0;
            $status_code=500;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }
    }

    public function registerPortal(Request $request){
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
            $newUser->profile_picture=$request->input('profile_picture');

            $type=$request->input('type');


            $token=str_shuffle(\Carbon\Carbon::now()."slohoneyarkoo".$newUser->email."crooogecbdhhd");//randomly picked
            $encrypted = Crypt::encrypt($token);

            $newUser->remember_token=$token;
            
            if($newUser->save()){
                if($type=='guide'){
                    $guide=Role::find(4);
                    $newUser->attachRole($guide);
                    DB::table('guides')
                    ->insert([
                        'id' => $newUser->id, 
                        'gov_registration_id' => $request->input('gov_registration_id'),
                        'address' => $request->input('address'),
                        'available_areas' => $request->input('available_areas'),
                        'languages' => $request->input('languages')
                    ]);
                }
                if($type=='sponsor'){
                    $sponsor=Role::find(6);
                    $newUser->attachRole($sponsor);
                    DB::table('sponsors')
                    ->insert([
                        'id' => $newUser->id, 
                        'link' => $request->input('link')
                    ]);
                }
                $businessuser=Role::find(3);
                $newUser->attachRole($businessuser);


                $message='Successfully registered! Please go to e-mails and verify your e-mail address.';
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
            $message='Unknown error!';
            $codex=0;
            $status_code=500;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }
    }

    public function verifyRegistration(Request $request){
        $email=$request->input('this_email');
        $token=$request->input('token');

        $user=DB::table('users')->where('email',$email)->first();
        $dbToken=$user->remember_token;

        try{
            if($token==$dbToken){
                $user=DB::table('users')
                ->where('email',$email)
                ->update(['remember_token'=>null,'active'=>'1']);
                $message='Registration was Successfully completed. Please login to continue.';
                $codex=1;
                $status_code=200;
                $data='""';
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                return $Respose;
        
            }else{
                $message='User had already completed registration or User was not found!';
                $codex=0;
                $status_code=404;
                $data='""';
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                return $Respose;
            }
        }catch(\Exception $ex){

            $message='Unknown error!';
            $codex=0;
            $status_code=500;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }

    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function index(){
        return json_encode(User::all());
    }

    public function show(){
        try{
            $user=JWTAuth::parseToken()->toUser();
            if(!$user){
                $message='Unknown error!';
                $codex=0;
                $status_code=500;
                $data='""';
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                return $Respose;
            }
            if(\Entrust::hasRole('traveler')){
                $message='Done!';
                $codex=1;
                $status_code=200;
                $data=$user;
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                return $Respose;
            }
                //return json_encode($user);
            else{
                $user_id=$user->id;
                $permissionArray=[];
                $key1=0;
                $rolesArray=[];
                $key2=0;
                $user_roles=User::find($user_id)->roles;
                foreach ($user_roles as $key => $value) {
                    $permissions=DB::table('permission_role')
                    ->join('roles','roles.id','=','permission_role.role_id')
                    ->where('roles.id',$value->id)//->permissions;
                    ->select('permission_role.permission_id')
                    ->get();
                    $rolesArray[$key2]=$user_roles[$key]->name;
                    $key2++;
                    $permissions = json_decode(json_encode($permissions), True);

                    foreach ($permissions as $key => $value) {
                        $permission_name=DB::table('permissions')
                        ->where('id',$value['permission_id'])//->permissions;
                        ->first()->name;
                        $permissionArray[$key1]=$permission_name;
                        $key1++;

                    }
                    //$permissionArray[$key1]=$permissions;
                    
                }
                

                $message='Done!';
                $codex=1;
                $status_code=200;
                $data0=json_encode($user);
                $data1=json_encode($rolesArray);
                $data2=json_encode($permissionArray);
                $data='{"user_data":'.$data0.',"roles":'.$data1.' ,"permissions":'.$data2.'}';
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :  '.$data.'}';
                return $Respose;
           
            }
                //return json_encode(array('user'=>$user,'roles'=>User::find($user->id)->roles));
        }catch(JWTException $ex){
            $message='Token problem! ';
            $codex=0;
            $status_code=401;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }catch(\Exception $ex){
            $message='Unknown error!';
            $codex=0;
            $status_code=500;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }

            
    }

    public function getToken(){
        
        try{

            $token = JWTAuth::getToken();
            if(!$token){
                $message='Unknown error!';
                $codex=0;
                $status_code=500;
                $data='""';
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                return $Respose;
            }
            $refreshedToken=JWTAuth::refresh($token);

        }catch(JWTException $ex){
            $message='Token problem! ';
            $codex=0;
            $status_code=401;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;

        }catch(\Exception $ex){
            $message='Unknown error!';
            $codex=0;
            $status_code=500;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }
        //return $this->response->array(compact('refreshedToken'));
        $message='Done!';
        $codex=1;
        $status_code=200;
        $data=json_encode(compact('refreshedToken'));
        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
        return $Respose;
    }

    public function destroy(){
        $user = JWTAuth::parseToken()->authenticate();
        if(!$user){
            //fail delete process
        }
    }
    //if not go wiith delete process


    public function logout(){
        
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                $message='Unown error!';
                $codex=0;
                $status_code=500;
                $data="";//compact($count);
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                return $Respose;
            }
            JWTAuth::invalidate();
        }catch(JWTException $ex){
            $message='Token problem! ';
            $codex=0;
            $status_code=401;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }catch(\Exception $ex){
            $message='Unknown error!';
            $codex=0;
            $status_code=500;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }

        $message='Done!';
        $codex=1;
        $status_code=200;
        $data='""';
        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
        return $Respose;
    }


    public function changePhoneNumber(Request $request){

        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                $message='Unown error!';
                $codex=0;
                $status_code=500;
                $data="";//compact($count);
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                return $Respose;
            }
            $new_phone_number=$request->input('new_phone_number');
            $user->telephone=$new_phone_number;
            $user->update();
        }catch(JWTException $ex){
            $message='Token problem! ';
            $codex=0;
            $status_code=401;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }catch(\Exception $ex){
            $message='Unknown error!';
            $codex=0;
            $status_code=500;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }

        $message='Done!';
        $codex=1;
        $status_code=200;
        $data='""';
        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
        return $Respose;
    }
}

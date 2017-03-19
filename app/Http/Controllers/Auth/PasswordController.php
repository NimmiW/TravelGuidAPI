<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use DB;
use Crypt;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use Dingo\Api\Routing\Helpers;
use Hash;
use App\User;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords,Helpers;

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware());
    }

    public function resetPassword(Request $request){//send email
        $email=$request->input('email');
        $user=DB::table('users')
            ->where('email','=',$email)->first();
        if(!$user)
            return 0;//no such user registered in the system

        //create a randomly shuffling string
        $token=str_shuffle(\Carbon\Carbon::now()."slow tharkoo".$user->id);
        $encrypted = Crypt::encrypt($token);
        //$decrypted = Crypt::decrypt($encrypted);
        //echo $encrypted;
        //echo $decrypted;
        DB::table('password_resets')->insert(
            ['email' => $email, 'token' => $encrypted]
        );   
        
        Mail::send('emails.passwordReset', ['name' => $user->name,'token'=>$encrypted,'email'=>$email], function ($m) use ($user) {
            $m->to($user->email, $user->name)->subject('SLTravelmate | Password Reset Confirmation Email');
        });

        return 1;//successfully email was sent

    }

    public function confirmResetPassword(Request $request){//when user clicks the email
        $token = $request->input('token');
        $email = $request->input('this_email');
        //$decrypted = Crypt::decrypt($token);
        $dbToken=DB::table('password_resets')
        ->where('email', '=', $email)
        ->orderBy('created_at', 'desc')->first()//equality is checked only for last token request
        ->token;
        $exxUser=User::where('email',$email)->first();

        if($token==$dbToken){
            $dummyPassword = str_shuffle("doorOpen".$exxUser->id."thakoo".\Carbon\Carbon::now());
            $exUser=User::where('email',$email)->update(['password'=>Hash::make($dummyPassword)]);
            //$exUser->password=Hash::make("loveyou");
            $credentials = ['email'=>$email,'password'=>$dummyPassword];

            try{
                if(!$user=JWTAuth::attempt($credentials)){

                    return "4";
                }

            }catch(JWTException $ex){
                return "2";

            }  //catch(\Exception $ex){
            //    return "0";
            //}

            return $this->response->array(compact('user'))->setStatusCode(200);
        }
        return 0;
    }

    public function changePassword(Request $request){
        try{
            $user=JWTAuth::parseToken()->toUser();
            if(!$user){
                return ErrorController::error500();
            }
             
        }catch(JWTException $ex){
            return ErrorController::error401();
        }catch(\Exception $ex){
            return ErrorController::error500();
        }
        $id=$user->id;

        
        try{
            $user=User::where('id',$id)->update(['password'=>Hash::make($request->input('password'))]);
            $message='Done!';
            $codex=1;
            $status_code=200;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :  '.$data.'}';
            return $Respose;
        }catch(\Exception $ex){
            return ErrorController::error500();
        }
        

    }
}

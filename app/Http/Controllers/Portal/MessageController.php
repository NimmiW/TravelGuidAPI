<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Item;
use App\Post;
use App\Like;
use App\User;
use App\Message;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ErrorController;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Portal\GCMController;


class MessageController extends Controller
{
    use Helpers;

    /*public function __construct(){
        $this->middleware(['role:traveler']);
    }*/
    public function getGuideList(){

        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }
            
            if (\Entrust::hasRole('admin')){
                
                
               $allGuides = DB::table('users')
                    ->join('role_user', 'users.id', '=', 'role_user.user_id')
                    ->join('guide_likes', 'users.id', '=', 'guide_likes.guide_id')
                    ->join('guides', 'users.id', '=', 'guides.id')
                    ->where('role_user.role_id',4)
                    ->select(
                        'guide_likes.guide_id', 
                        DB::raw('CONCAT(users.name," " ,users.sur_name)   as name'),
                        'users.is_online','users.profile_picture',
                        DB::raw('ROUND(DATEDIFF(CURDATE(),users.date_of_birth)/366) as age'),
                        'guides.gov_registration_id',
                        'guides.address',
                        'guides.languages',
                        'guides.available_areas',
                        'users.email',
                        'users.telephone',
                        DB::raw('count(*) as no_of_likes'),
                        'active',
                        'reviewedByAdmin'
                      )
                    ->groupBy('guide_likes.guide_id') 
                    ->get();

                $guides = json_decode(json_encode($allGuides), True);//convert to an associative array

                foreach ($guides as $key => $value) {
                    
                    $guides[$key]['guide_id']=intval($guides[$key]['guide_id']);
                    $guides[$key]['languages']=explode(",",$guides[$key]['languages']);
                    $guides[$key]['available_areas']=explode(",",$guides[$key]['available_areas']);
                    $guides[$key]['age']=intval($guides[$key]['age']);
                    $guides[$key]['is_online']=intval($guides[$key]['is_online']);
                    $guides[$key]['no_of_likes']=intval($guides[$key]['no_of_likes']);

                }

                $allGuides = json_encode($guides);
                $allGuides = str_replace("\\", "", $allGuides);
                
                $message='Done!';
                $codex=1;
                $status_code=200;
                $data=$allGuides;
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

    
    public function getOnlineGuideList(){

    }


    public function getMessageBoxOfUser(){
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }
            
            if (\Entrust::hasRole('guide')){
                $user_id=$user->id;
                
                $guides = DB::table('users')
                    ->join('messages', 'users.id', '=', 'messages.guide_id')
                    ->where('messages.guide_id','=',$user_id)
                    ->select('messages.traveller_id', 'users.name',DB::raw('count(*) as message_count'))
                    ->groupBy('messages.traveller_id') 
                    ->get();


                $guides = json_decode(json_encode($guides), True);

                foreach ($guides as $key => $value) {
                    $new_messge_count=DB::table('messages')
                    ->where('guide_id',$user_id)
                    ->where('traveller_id',$guides[$key]['traveller_id'])
                    ->where('is_sent_by_guide',0)
                    ->where('review',0)
                    ->count();
                    $guides[$key]['new_messge_count']=$new_messge_count;
                }

                foreach ($guides as $key => $value) {
                    $last=DB::table('messages')
                    ->where('guide_id',$user_id)
                    ->where('traveller_id',$guides[$key]['traveller_id'])
                    ->where('is_sent_by_guide',1)
                    ->orderBy('id', 'desc')->first();
                    $last = json_decode(json_encode($last), True);
                    $guides[$key]['seen_status']=$last;//['review'];
                }

                foreach ($guides as $key => $value) {
                    $travelerName=User::find($guides[$key]['traveller_id'])->name;
                    $guides[$key]['traveller_id']=intval($guides[$key]['traveller_id']);
                    $guides[$key]['message_count']=intval($guides[$key]['message_count']);
                    $guides[$key]['new_messge_count']=intval($guides[$key]['new_messge_count']);
                    $guides[$key]['name']=$travelerName;
                }


                $guides=json_encode($guides) ;
                $guides = str_replace("\\", "", $guides);

                $message='Done!';
                $codex=1;
                $status_code=200;
                $data=$guides;
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                return $Respose;
                //return $guides;
                
            }else{
                return ErrorController::error405();
            }

        }catch(JWTException $ex){
            return ErrorController::error401();
        }catch(\Exception $ex){
            return ErrorController::error500();
        }



    }

    public function getAllMessagesOfUser(){
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }
            
            
            if (\Entrust::hasRole('guide')){
                $user_id=$user->id;
                
                $messages = DB::table('users')
                    ->join('messages', 'users.id', '=', 'messages.guide_id')
                    ->where('messages.guide_id','=',$user_id)
                    ->select('messages.id', 'messages.traveller_id',
                        'users.name','sent_time',
                        'messages.content','is_sent_by_guide','messages.review')
                    ->get();

                $messages = json_decode(json_encode($messages), True);

                foreach ($messages as $key => $value) {
                    $travelerName=User::find($messages[$key]['traveller_id'])->name;
                    $messages[$key]['id']=intval($messages[$key]['id']);
                    $messages[$key]['traveller_id']=intval($messages[$key]['traveller_id']);
                    $messages[$key]['is_sent_by_guide']=intval($messages[$key]['is_sent_by_guide']);
                    $messages[$key]['review']=intval($messages[$key]['review']);
                    $messages[$key]['name']=$travelerName;
                }

                $messages=json_encode($messages) ;
                $messages = str_replace("\\", "", $messages);

                $message='Done!';
                $codex=1;
                $status_code=200;
                $data=$messages;
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

    public function saveMessage(Request $request){

        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }
            
            if (\Entrust::hasRole('guide')){
                
                $newMessage = new Message();
        
                $newMessage->traveller_id = $request->input('traveller_id');//typo traveller, bt cannot recorrect it
                $newMessage->content = $request->input('content');
                $newMessage->guide_id = $user->id;
                $newMessage->is_sent_by_guide=1; //guide is sending the message

                try{
                    if($newMessage->save()){

                        $traveler_dev_id=User::find($request->input('traveller_id'))->device_id;
                        if(!$traveler_dev_id==null){
                            $gcm=new GCMController();
                            $gcm->sendGCM($traveler_dev_id);
                        }

                        $message='Done!';
                        $codex=1;
                        $status_code=200;
                        $data='""';
                        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                        return $Respose; 
                    }
                    else{
                        return ErrorController::error500();
                    }
                }catch(\Exception $ex){
                        $message='Internal Server Error!';
                        $codex=0;
                        $status_code=500;
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

    public function saveSeenStatus(Request $request){
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }
            
            if (\Entrust::hasRole('guide')){
                $traveller=$request->input('traveller_id');
                Message::where('traveller_id',$traveller)->where('is_sent_by_guide',0)->update(['review'=>1]);
                $message='Done!';
                $codex=1;
                $status_code=200;
                $data='""';
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


    public function getAllMessagesOfUserAndTraveler(Request $request){
       try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }
            
            
            if (\Entrust::hasRole('guide')){
                $user_id=$user->id;
                
                $messages = DB::table('users')
                    ->join('messages', 'users.id', '=', 'messages.guide_id')
                    ->where('messages.guide_id','=',$user_id)
                    ->where('messages.traveller_id','=',$request->input('traveller_id'))
                    ->select('messages.id', 'messages.traveller_id',
                        'users.name','sent_time',
                        'messages.content','is_sent_by_guide','messages.review')
                    ->get();

                $messages = json_decode(json_encode($messages), True);

                foreach ($messages as $key => $value) {
                    $travelerName=User::find($messages[$key]['traveller_id'])->name;
                    $messages[$key]['id']=intval($messages[$key]['id']);
                    $messages[$key]['traveller_id']=intval($messages[$key]['traveller_id']);
                    $messages[$key]['is_sent_by_guide']=intval($messages[$key]['is_sent_by_guide']);
                    $messages[$key]['review']=intval($messages[$key]['review']);
                    $messages[$key]['name']=$travelerName;
                }

                $messages=json_encode($messages) ;
                $messages = str_replace("\\", "", $messages);

                $message='Done!';
                $codex=1;
                $status_code=200;
                $data=$messages;
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


}

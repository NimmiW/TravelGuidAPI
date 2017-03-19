<?php

namespace App\Http\Controllers\Client\Post;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Item;
use App\Post;
use App\Like;
use App\User;
use App\Message;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use Dingo\Api\Routing\Helpers;


class MessageController extends Controller
{
    use Helpers;

    /*public function __construct(){
        $this->middleware(['role:traveler']);
    }*/
    public function getGuideList(){

        $allGuides = DB::table('users')
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('guide_likes', 'users.id', '=', 'guide_likes.guide_id')
            ->where('users.active',1)
            ->where('users.reviewedByAdmin',1)
            ->join('guides', 'users.id', '=', 'guides.id')
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
                DB::raw('count(*) as no_of_likes')
              )
            ->groupBy('guide_likes.guide_id') 
            ->get();

        $guides = json_decode(json_encode($allGuides), True);//convert to an associative array

        foreach ($guides as $key => $value) {
            $no_of_likes=DB::table('guide_likes')
                ->where('guide_id',$guides[$key]['guide_id'])->count();
            $guides[$key]['no_of_likes']=$no_of_likes;
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
        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$allGuides.'}';
        return $Respose;
        
    }


    public function getOnlineGuideList(){

    }


    public function getMessageBoxOfUser(){
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
            
            if (\Entrust::hasRole('traveler')){
                $user_id=$user->id;
                
                $guides = DB::table('users')
                    ->join('messages', 'users.id', '=', 'messages.traveller_id')
                    ->where('messages.traveller_id','=',$user_id)
                    ->select('messages.guide_id', 'users.name',DB::raw('count(*) as message_count'))
                    ->groupBy('messages.guide_id') 
                    ->get();


                $guides = json_decode(json_encode($guides), True);

                foreach ($guides as $key => $value) {
                    $new_messge_count=DB::table('messages')
                    ->where('traveller_id',$user_id)
                    ->where('guide_id',$guides[$key]['guide_id'])
                    ->where('is_sent_by_guide',1)
                    ->where('review',0)
                    ->count();
                    $guides[$key]['new_messge_count']=$new_messge_count;
                }

                foreach ($guides as $key => $value) {
                    $last=DB::table('messages')
                    ->where('traveller_id',$user_id)
                    ->where('guide_id',$guides[$key]['guide_id'])
                    ->where('is_sent_by_guide',0)
                    ->orderBy('sent_time', 'desc')->first();
                    $last = json_decode(json_encode($last), True);
                    $guides[$key]['seen_status']=$last['review'];
                }

                foreach ($guides as $key => $value) {
                    $travelerName=User::find($guides[$key]['guide_id'])->name;
                    $guides[$key]['guide_id']=intval($guides[$key]['guide_id']);
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
                $message='Action not allowed!';
                $codex=0;
                $status_code=405;
                $data='""';
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                return $Respose;
            }

        }catch(JWTException $ex){
            $message='Token problem! ';
            $codex=0;
            $status_code=416;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }catch(\Exception $ex){
            $message='Unown error!';
            $codex=0;
            $status_code=500;
            $data="";//compact($count);
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
            return $Respose;
        }



    }

    public function getAllMessagesOfUser(){
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
            
            
            if (\Entrust::hasRole('traveler')){
                $user_id=$user->id;
                
                $messages = DB::table('users')
                    ->join('messages', 'users.id', '=', 'messages.traveller_id')
                    ->where('messages.traveller_id','=',$user_id)
                    ->select('messages.id', 'messages.guide_id',
                        'users.name','sent_time',
                        'messages.content','is_sent_by_guide','messages.review')
                    ->get();

                $messages = json_decode(json_encode($messages), True);

                foreach ($messages as $key => $value) {
                    $travelerName=User::find($messages[$key]['guide_id'])->name;
                    $messages[$key]['id']=intval($messages[$key]['id']);
                    $messages[$key]['guide_id']=intval($messages[$key]['guide_id']);
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
                $message='Action not allowed!';
                $codex=0;
                $status_code=405;
                $data='""';
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                return $Respose;
            }

        }catch(JWTException $ex){
            $message='Token problem! ';
            $codex=0;
            $status_code=416;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }catch(\Exception $ex){
            $message='Unown error!';
            $codex=0;
            $status_code=500;
            $data="";//compact($count);
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
            return $Respose;
        }


    }

    public function saveMessage(Request $request){

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
            
            if (\Entrust::hasRole('traveler')){
                
                $newMessage = new Message();
        
                $newMessage->guide_id = $request->input('guide_id');
                $newMessage->content = $request->input('content');
                $newMessage->traveller_id = $user->id;//typo traveller, bt cannot recorrect it
                $newMessage->is_sent_by_guide=0; //user is sending the message
                //$newMessage->active = $request->input('active');
                try{
                    if($newMessage->save()){
                        $message='Done!';
                        $codex=1;
                        $status_code=200;
                        $data='""';
                        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                        return $Respose; 
                    }
                    else{
                        $message='Unown error!';
                        $codex=0;
                        $status_code=500;
                        $data="";//compact($count);
                        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                        return $Respose;
                    }
                }catch(\Exception $ex){
                        $message='Internal Server Error!';
                        $codex=0;
                        $status_code=500;
                        $data="";//compact($count);
                        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                        return $Respose;
                }              
            }else{
                $message='Action not allowed!';
                $codex=0;
                $status_code=405;
                $data='""';
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                return $Respose;
            }

        }catch(JWTException $ex){
            $message='Token problem! ';
            $codex=0;
            $status_code=416;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }catch(\Exception $ex){
            $message='Unknown error!';
            $codex=0;
            $status_code=416;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }

    }

    public function saveSeenStatus(Request $request){
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
            
            if (\Entrust::hasRole('traveler')){
                $guide=$request->input('guide_id');
                Message::where('guide_id',$guide)->where('is_sent_by_guide',1)->update(['review'=>1]);
                $message='Done!';
                $codex=1;
                $status_code=200;
                $data='""';
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                return $Respose; 
             
            }else{
                $message='Action not allowed!';
                $codex=0;
                $status_code=405;
                $data='""';
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                return $Respose;
            }

        }catch(JWTException $ex){
            $message='Token problem! ';
            $codex=0;
            $status_code=416;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }catch(\Exception $ex){
            $message='Unknown error!';
            $codex=0;
            $status_code=416;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }
    }

    public function getAllMessagesOfUserAndGuide(Request $request){
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
            
            
            if (\Entrust::hasRole('traveler')){
                $user_id=$user->id;
                $guide_id=$request->input('guide_id');
                
                $messages = DB::table('messages')
                    ->where('traveller_id','=',$user_id)
                    ->where('guide_id',$guide_id)
                    ->select('id', 
                        'guide_id',
                        'sent_time',
                        'content',
                        'is_sent_by_guide',
                        'review')
                    ->get();

                $messages = json_decode(json_encode($messages), True);

                foreach ($messages as $key => $value) {
                    
                    $messages[$key]['id']=intval($messages[$key]['id']);
                    $messages[$key]['guide_id']=intval($messages[$key]['guide_id']);
                    $messages[$key]['is_sent_by_guide']=intval($messages[$key]['is_sent_by_guide']);
                    $messages[$key]['review']=intval($messages[$key]['review']);
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
                $message='Action not allowed!';
                $codex=0;
                $status_code=405;
                $data='""';
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                return $Respose;
            }

        }catch(JWTException $ex){
            $message='Token problem! ';
            $codex=0;
            $status_code=416;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }catch(\Exception $ex){
            $message='Unown error!';
            $codex=0;
            $status_code=500;
            $data="";//compact($count);
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
            return $Respose;
        }


    }






    public function getGuideListMobileApp(){

        $allGuides = DB::table('users')
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('guide_likes', 'users.id', '=', 'guide_likes.guide_id')
            ->where('users.reviewedByAdmin',1)
            ->join('guides', 'users.id', '=', 'guides.id')
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
                //'users.active',
                DB::raw('count(*) as no_of_likes')
              )
            ->groupBy('guide_likes.guide_id') 
            ->get();

        $guides = json_decode(json_encode($allGuides), True);//convert to an associative array

        foreach ($guides as $key => $value) {
            $no_of_likes=DB::table('guide_likes')
                ->where('guide_id',$guides[$key]['guide_id'])->count();
            $guides[$key]['no_of_likes']=$no_of_likes;
            $active=DB::table('users')
                    ->where('id',$guides[$key]['guide_id'])
                    ->first()->active;
            $guides[$key]['active']=intval($active);
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
        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$allGuides.'}';
        return $Respose;
        
    }


    public function saveMessageMobileApp(Request $request){

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
            
            if (\Entrust::hasRole('traveler')){
                
                $newMessage = new Message();
        
                $newMessage->guide_id = $request->input('guide_id');
                $newMessage->content = $request->input('content');
                $newMessage->traveller_id = $user->id;//typo traveller, bt cannot recorrect it
                $newMessage->is_sent_by_guide=0; //user is sending the message
                //$newMessage->active = $request->input('active');
                //if(isset($_POST['device_id'])){
                    DB::table('users')
                    ->where('id',$user->id)
                    ->update([
                        'device_id'=>$request->input('device_id')
                    ]);
                //}
                
                try{
                    if($newMessage->save()){
                        $message='Done!';
                        $codex=1;
                        $status_code=200;
                        $data='""';
                        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                        return $Respose; 
                    }
                    else{
                        $message='Unown error!';
                        $codex=0;
                        $status_code=500;
                        $data="";//compact($count);
                        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                        return $Respose;
                    }
                }catch(\Exception $ex){
                        $message='Internal Server Error!';
                        $codex=0;
                        $status_code=500;
                        $data="";//compact($count);
                        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                        return $Respose;
                }             
            }else{
                $message='Action not allowed!';
                $codex=0;
                $status_code=405;
                $data='""';
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                return $Respose;
            }

        }catch(JWTException $ex){
            $message='Token problem! ';
            $codex=0;
            $status_code=416;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }catch(\Exception $ex){
            $message='Unknown error!';
            $codex=0;
            $status_code=416;
            $data='""';
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
            return $Respose;
        }

    }




}

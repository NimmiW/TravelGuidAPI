<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Item;
use App\Post;
use App\Like;
use App\User;
use App\SlideShowItem;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use Dingo\Api\Routing\Helpers;


class PostController extends Controller
{
	use Helpers;

	/*public function __construct(){
        $this->middleware(['role:traveler']);
    }*/

    public function getNotReviewedPosts(){
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                $message='Unown Error!';
                $codex=0;
                $status_code=500;
                $data="";
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                return $Respose;
            }
            
            if (\Entrust::hasRole('admin')){
                $posts = DB::table('posts')
                ->where('active',1)
                ->where('reviewedByAdmin',0)
                ->get();

                $posts = json_decode(json_encode($posts), True);
                foreach ($posts as $key => $value1) {
                    $posts[$key]['id']=intval($posts[$key]['id']);
                    $posts[$key]['guide_id']=intval($posts[$key]['guide_id']);
                    $posts[$key]['author_id']=intval($posts[$key]['author_id']);

                }

                $posts=json_encode($posts) ;
                $posts = str_replace("\\", "", $posts);
                
                $message='Done';
                $codex=1;
                $status_code=200;
                $data=$posts;
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
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

	public function getAllPostsForGuide(Request $response){
		try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                $message='Unown Error!';
                $codex=0;
                $status_code=500;
                $data="";
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                return $Respose;
            }
            
            if (\Entrust::hasRole(['admin','guide'])){
            	$guide_id=$response->input('guide_id');
            	
            	$posts = DB::table('posts')
				->where('guide_id', $guide_id)
				->where('active',1)
				->get();

                $posts = json_decode(json_encode($posts), True);
                foreach ($posts as $key => $value1) {
                    $posts[$key]['id']=intval($posts[$key]['id']);
                    $posts[$key]['guide_id']=intval($posts[$key]['guide_id']);
                    $posts[$key]['author_id']=intval($posts[$key]['author_id']);

                }

				$posts=json_encode($posts) ;
        		$posts = str_replace("\\", "", $posts);
				
                $message='Done';
                $codex=1;
                $status_code=200;
                $data=$posts;
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
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



	public function deletePost(Request $request){

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
            	$post_id=$request->input('post_id'); 

				$post = DB::table('posts')
				->where('author_id', $user_id)
				->where('id', $post_id)
				->update(['active' => 0]);
				if($post==0){
                    $message='Post was already removed!';//post is already removed by the user
                    $codex=0;
                    $status_code=500;
                    $data="";
                    $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                    return $Respose;
                }
		        else {
                    $message='Post was successfully removed!';
                    $codex=1;
                    $status_code=200;
                    $data="";//compact($count);
                    $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                    return $Respose;
                }
    			
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


//*****************************************FOR GUIDES*******************************************

  
    public function deletePostForGuide(Request $request){

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
                $post_id=$request->input('post_id'); 

                $post = DB::table('posts')
                ->where('author_id', $user_id)
                ->where('id', $post_id)
                ->update(['active' => 0]);
                if($post==0){
                    $message='Post was already removed!';//post is already removed by the user
                    $codex=0;
                    $status_code=500;
                    $data="";
                    $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                    return $Respose;
                }
                else {
                    $message='Post was successfully removed!';
                    $codex=1;
                    $status_code=200;
                    $data="";//compact($count);
                    $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                    return $Respose;
                }
                
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

    public function setReviewedByAdminStatusToPost(Request $request){
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }
            
            if (\Entrust::hasRole('admin')){

                $post_id=$request->input('post_id');
                $post_accept_decline_status=$request->input('post_accept_decline_status');

                if($post_accept_decline_status==1){
                    $user=Post::where('id',$post_id)->update(['reviewedByAdmin'=>1,'active'=>1]);
                    $message='The post was accepted!';
                    $codex=1;
                    $status_code=200;
                    $data='""';
                    $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" : '.$data.'}';
                    return $Respose;
                }else{
                    $user=Post::where('id',$post_id)->update(['reviewedByAdmin'=>1,'active'=>0]);
                    $message='The post was rejected!';
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

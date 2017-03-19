<?php

namespace App\Http\Controllers\Client\Post;

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

	public function getPostsForItem($item_id){
		$posts = DB::table('posts')
			->where('posts.item_id','=',$item_id)
			->where('posts.reviewedByAdmin','=',1)
            ->where('posts.active','=',1)
            ->join('users', 'users.id', '=', 'posts.author_id')
            ->join('items', 'posts.item_id', '=', 'items.id')
            ->select('posts.id','posts.item_id','posts.author_id','posts.title','posts.body',
                'posts.created_at','posts.updated_at',
                'posts.slug','users.name')
            ->get();
        
        $posts = json_decode(json_encode($posts), True);
        foreach ($posts as $key => $value1) {
            $posts[$key]['id']=intval($posts[$key]['id']);
            $posts[$key]['item_id']=intval($posts[$key]['item_id']);
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
	}

    public function getPostsForGuide($guide_id){
        $posts = DB::table('posts')
            ->where('posts.guide_id','=',$guide_id)
            ->where('posts.reviewedByAdmin','=',1)
            ->where('posts.active',1)
            //->select('posts.id','posts.guide_id','posts.author_id','posts.title','posts.body','posts.slug')
            ->get();
            
            
        $posts = json_decode(json_encode($posts), True);
        foreach ($posts as $key => $value1) {
            $posts[$key]['id']=intval($posts[$key]['author_id']);
            $author=User::find($posts[$key]['id']);
            $posts[$key]['name']=$author->name;
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
    }

    public function getPostsForGuideMobileApp(Request $request){
        $posts = DB::table('posts')
            ->where('posts.guide_id','=',$request->input('guide_id'))
            ->where('posts.reviewedByAdmin','=',1)
            ->get();
            
            
        $posts = json_decode(json_encode($posts), True);
        foreach ($posts as $key => $value1) {
            $posts[$key]['id']=intval($posts[$key]['author_id']);
            $author=User::find($posts[$key]['id']);
            $posts[$key]['name']=$author->name;
            $posts[$key]['guide_id']=intval($posts[$key]['guide_id']);
            $posts[$key]['author_id']=intval($posts[$key]['author_id']);
            $posts[$key]['active']=intval($posts[$key]['active']);
            $x= strip_tags($posts[$key]['body']);
            $posts[$key]['post_body_for_mobile_app']=$x;

        }
        $posts=json_encode($posts) ;
        $posts = str_replace("\\", "", $posts);
        
        $message='Done';
        $codex=1;
        $status_code=200;
        $data=$posts;
        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
        return $Respose;
    }

	public function getAllPostsOfUser(){
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
            	
            	$posts = DB::table('posts')
				->where('author_id', $user_id)
				->where('active',1)
				->get();

                $posts = json_decode(json_encode($posts), True);
                foreach ($posts as $key => $value1) {
                    $posts[$key]['id']=intval($posts[$key]['id']);
                    $posts[$key]['item_id']=intval($posts[$key]['item_id']);
                    $posts[$key]['author_id']=intval($posts[$key]['author_id']);
                    $x= strip_tags($posts[$key]['body']);
                    $posts[$key1]['post_body_for_mobile_app']=$x;

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

 	public function savePost(Request $request){

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
            	
            	$newPost = new Post();
        
				$newPost->title = $request->input('title');
				$newPost->body = $request->input('body');
				$newPost->item_id = $request->input('item_id');
				$newPost->slug = str_slug($newPost->title);
				$newPost->author_id = $user->id;
				//$newPost->active = $request->input('active');
                try{
    				if($newPost->save()){
                        $message='Post saved successfully. Your post will be reveiwed by an admin soon!';
                        $codex=1;
                        $status_code=200;
                        $data="";//compact($count);
                        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                        return $Respose;
                    }
    		        	
    		        else {
                        $message='Unown Error!';
                        $codex=0;
                        $status_code=500;
                        $data="";//compact($count);
                        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                        return $Respose;
                    }
                }catch(\Exception $ex){
                    $message='Already liked!';
                    $codex=0;
                    $status_code=416;
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

	public function editPost(Request $request){
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
            	$title = $request->input('title');
				$body = $request->input('body');
				$slug = str_slug($title);

				$post = DB::table('posts')
				->where('author_id', $user_id)//to restrict the user from deleting other's posts
				->where('id', $post_id)
				->update(['title' => $title,'body'=>$body,'slug'=>$slug]);
				if($post==0){
                    $message='Post not found!';//post is already removed by the user
                    $codex=0;
                    $status_code=404;
                    $data="";
                    $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                    return $Respose;
                }
		        else {
                    $message='Post was edited successfully!';
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
            $message='Unown error!';
            $codex=0;
            $status_code=500;
            $data="";//compact($count);
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
            return $Respose;
        }

	}

//*****************************************FOR GUIDES*******************************************

    public function savePostForGuide(Request $request){

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
                
                $newPost = new Post();
        
                $newPost->title = $request->input('title');
                $newPost->body = $request->input('body');
                $newPost->guide_id = $request->input('guide_id');
                $newPost->slug = str_slug($newPost->title);
                $newPost->author_id = $user->id;
                //$newPost->active = $request->input('active');
                try{
                    if($newPost->save()){
                        $message='Post saved successfully. Your post will be reveiwed by an admin soon!';
                        $codex=1;
                        $status_code=200;
                        $data="";//compact($count);
                        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                        return $Respose;
                    }
                        
                    else {
                        $message='Unown Error!';
                        $codex=0;
                        $status_code=500;
                        $data="";//compact($count);
                        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                        return $Respose;
                    }
                }catch(\Exception $ex){
                    $message='Already liked!';
                    $codex=0;
                    $status_code=416;
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

    public function editPostForGuide(Request $request){
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
                $title = $request->input('title');
                $body = $request->input('body');
                $slug = str_slug($title);

                $post = DB::table('posts')
                ->where('author_id', $user_id)//to restrict the user from deleting other's posts
                ->where('id', $post_id)
                ->update(['title' => $title,'body'=>$body,'slug'=>$slug]);
                if($post==0){
                    $message='Post not found!';//post is already removed by the user
                    $codex=0;
                    $status_code=404;
                    $data="";
                    $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                    return $Respose;
                }
                else {
                    $message='Post was edited successfully!';
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
            $message='Unown error!';
            $codex=0;
            $status_code=500;
            $data="";//compact($count);
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
            return $Respose;
        }

    }



}

<?php

namespace App\Http\Controllers\Client\Get;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Item;
use App\Post;
use App\Like;
use App\GuideLike;
use App\User;
use App\Category;
use App\SlideShowItem;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ErrorController;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use Dingo\Api\Routing\Helpers;


class ItemController extends Controller
{
	use Helpers;

	public function incrementNoOfViews(Request $request){
		DB::table('items')
		->where('id', $request->input('item_id'))
		->increment('no_of_views');
	}

    public function getAllItemsOfCategory($category_ID){
		$items = Item::where('category_ID','=',$category_ID)->where('reviewedByAdmin','=',1)
		->where('active',1)
		->get();
		foreach ($items as $key => $value) {
			$items[$key]['id']=intval($items[$key]['id']);
			$items[$key]['category_ID']=intval($items[$key]['category_ID']);
			$category=Category::where('id',$items[$key]['category_ID'])->get();
			$category_name=$category[0]->category_name;
			$items[$key]['category_name']=$category_name;
			$items[$key]['latitude']=strval($items[$key]['latitude']);
			$items[$key]['longitude']=strval($items[$key]['longitude']);
			$items[$key]['no_of_views']=floatval($items[$key]['no_of_views']);
			$items[$key]['review']=floatval($items[$key]['review']);
			$slideshowItems=SlideShowItem::where('item_ID','=',$items[$key]->id)->get();
			$slideshowItems = json_decode(json_encode($slideshowItems), True);
			foreach ($slideshowItems as $key1 => $value1) {
				$slideshowItems[$key1]['slideshow_item_ID']=$slideshowItems[$key1]['id'];
				$slideshowItems[$key1]['type']=intval($slideshowItems[$key1]['type']);
				$slideshowItems[$key1]['item_ID']=intval($slideshowItems[$key1]['item_ID']);
				$slideshowItems[$key1]['slideshow_item_ID']=intval($slideshowItems[$key1]['slideshow_item_ID']);
			}
			$items[$key]['uploads']=$slideshowItems;
		}

		foreach ($items as $key => $value) {
			$posts=Post::where('item_id','=',$items[$key]->id)->where('reviewedByAdmin','=',1)
			->where('active',1)
			->get();
			$items[$key]['posts']=$posts;
			foreach ($posts as $key1 => $value1) {
				$posts[$key1]['item_id']=intval($posts[$key1]['item_id']);
				$posts[$key1]['author_id']=intval($posts[$key1]['author_id']);
				$author_name=User::where('id',$posts[$key1]['author_id'])->first()->name;
				$posts[$key1]['author_name']=$author_name;
				$x= strip_tags($posts[$key1]['body']);
				$posts[$key1]['post_body_for_mobile_app']=$x;

			}

		}

		foreach ($items as $key => $value) {
			$likes = DB::table('likes')
			->where('item_id','=',$items[$key]->id)
            ->select('user_id')
            ->get();
            $likes = json_decode(json_encode($likes), True);
            $array=[];
            foreach ($likes as $key1 => $value1) {
					$array[$key1]=intval($likes[$key1]['user_id']);

			}
			$items[$key]['likes']=$array;
			$items[$key]['no_of_likes']=intval(count($array));
			
		}
		$items = json_encode($items);
		$items = str_replace("\\", "", $items);


		$message='Done';
		$codex=1;
		$status_code=200;
		$data=$items;
		$Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
		return $Respose;
	}

	public function getAllItems(){
		$items = Item::where('reviewedByAdmin','=',1)
		->where('active',1)
		->get();
		foreach ($items as $key => $value) {
			$items[$key]['id']=intval($items[$key]['id']);
			$items[$key]['category_ID']=intval($items[$key]['category_ID']);
			$category=Category::where('id',$items[$key]['category_ID'])->get();
			$category_name=$category[0]->category_name;
			$items[$key]['category_name']=$category_name;
			$items[$key]['latitude']=strval($items[$key]['latitude']);
			$items[$key]['longitude']=strval($items[$key]['longitude']);
			$items[$key]['no_of_views']=floatval($items[$key]['no_of_views']);
			$items[$key]['review']=floatval($items[$key]['review']);
			$slideshowItems=SlideShowItem::where('item_ID','=',$items[$key]->id)->get();
			$slideshowItems = json_decode(json_encode($slideshowItems), True);
			foreach ($slideshowItems as $key1 => $value1) {
				$slideshowItems[$key1]['slideshow_item_ID']=$slideshowItems[$key1]['id'];
				$slideshowItems[$key1]['type']=intval($slideshowItems[$key1]['type']);
				$slideshowItems[$key1]['item_ID']=intval($slideshowItems[$key1]['item_ID']);
				$slideshowItems[$key1]['slideshow_item_ID']=intval($slideshowItems[$key1]['slideshow_item_ID']);
			}
			$items[$key]['uploads']=$slideshowItems;
		}

		foreach ($items as $key => $value) {
			$posts=Post::where('item_id','=',$items[$key]->id)->where('reviewedByAdmin','=',1)
			->where('active',1)
			->get();
			$items[$key]['posts']=$posts;
			foreach ($posts as $key1 => $value1) {
				$posts[$key1]['item_id']=intval($posts[$key1]['item_id']);
				$posts[$key1]['author_id']=intval($posts[$key1]['author_id']);
				$author_name=User::where('id',$posts[$key1]['author_id'])->first()->name;
				$posts[$key1]['author_name']=$author_name;
				$x= strip_tags($posts[$key1]['body']);
				$posts[$key1]['post_body_for_mobile_app']=$x;

			}

		}

		foreach ($items as $key => $value) {
			$likes = DB::table('likes')
			->where('item_id','=',$items[$key]->id)
            ->select('user_id')
            ->get();
            $likes = json_decode(json_encode($likes), True);
            $array=[];
            foreach ($likes as $key1 => $value1) {
					$array[$key1]=intval($likes[$key1]['user_id']);

			}
			$items[$key]['likes']=$array;
			$items[$key]['no_of_likes']=intval(count($array));
			
		}
		$items = json_encode($items);
		$items = str_replace("\\", "", $items);


		$message='Done';
		$codex=1;
		$status_code=200;
		$data=$items;
		$Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
		return $Respose;
	}

	public function setLike(Request $request){
		
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
            	//$user_id=$request->input('user_id');
            	$user_id=$user->id;
		        $item_id=$request->input('item_id');

				$newLike = new Like();
		        $newLike->user_id=$user_id;
				$newLike->item_id=$item_id;

				try{
					if($newLike->save()){

						//$count=Like::where('item_id',$item_id)->count();
						$message='Done!';
						$codex=1;
						$status_code=200;
						$data="";
						$Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
						return $Respose;
					}
			        else {
			        	//$count=Like::where('item_id',$item_id)->count();
			        	$message='Unown Error!';
						$codex=0;
						$status_code=500;
						$data="";
						$Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
						return $Respose;
			        }
		    	}catch(\Exception $ex){
		    		//$count=Like::where('item_id',$item_id)->count();
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

	public function removeLike(Request $request){
        
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
		        $item_id=$request->input('item_id');

				$likes = Like::where('user_id','=',$user_id)->where('item_id','=',$item_id)->delete();
				if($likes==0){
					$message='Like was not found!';
					$codex=0;
					$status_code=404;
					$data='""';
					$Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
					return $Respose;
				}
		        	
		        else {
		        	$message='Done!';
					$codex=1;
					$status_code=200;
					$data='""';
					$Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
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

	public function getLikeStatusOfUserForItem(Request $request){

        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }
            
            if (\Entrust::hasRole('traveler')){
                $item_id=$request->input('item_id');
                $is_liked=DB::table('likes')
                ->where('item_id',$item_id)
                ->where('user_id',$user->id)
                ->first();
                if($is_liked){
                	$message='Done!';
	                $codex=1;
	                $status_code=200;
	                $data='{"is_liked":'.intval(1).'}';
	                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
	                return $Respose; 
                }else{
                	$message='Done!';
	                $codex=1;
	                $status_code=200;
	                $data='{"is_liked":'.intval(0).'}';
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
	public function getLikeStatusOfUserForItemWithoutAuthorization(Request $request){

		try{
                $item_id=$request->input('item_id');
                $user_id=$request->input('user_id');
                $is_liked=DB::table('likes')
                ->where('item_id',$item_id)
                ->where('user_id',$user_id)
                ->first();
                if($is_liked){
                	$message='Done!';
	                $codex=1;
	                $status_code=200;
	                $data='{"is_liked":'.intval(1).'}';
	                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
	                return $Respose; 
                }else{
                	$message='Done!';
	                $codex=1;
	                $status_code=200;
	                $data='{"is_liked":'.intval(0).'}';
	                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
	                return $Respose;
                }
       

        
        }catch(\Exception $ex){
            return ErrorController::error500();
        }
	}
//------------------------------LIKES FOR GUIDES----------------------------------------------------------

	public function setLikeForGuide(Request $request){
		
		try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                $message='Unown error!';
				$codex=0;
				$status_code=500;
				$data="";
				$Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
				return $Respose;
            }
            
            if (\Entrust::hasRole('traveler')){

            	$traveler_id=$user->id;
		        $guide_id=$request->input('guide_id');

				$newLike = new GuideLike();
		        $newLike->traveler_id=$traveler_id;
				$newLike->guide_id=$guide_id;

				try{
					if($newLike->save()){

						$message='Done!';
						$codex=1;
						$status_code=200;
						$data="";
						$Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
						return $Respose;
					}
			        else {

			        	$message='Unown Error!';
						$codex=0;
						$status_code=500;
						$data="";
						$Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
						return $Respose;
			        }
		    	}catch(\Exception $ex){

		    		$message='Already liked!';
					$codex=0;
					$status_code=416;
					$data="";
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

	public function removeLikeForGuide(Request $request){
        
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
            	$traveler_id=$user->id;
		        $guide_id=$request->input('guide_id');

				$likes = GuideLike::where('traveler_id','=',$traveler_id)->where('guide_id','=',$guide_id)->delete();
				if($likes==0){
					$message='Like was not found';
					$codex=0;
					$status_code=404;
					$data='""';
					$Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
					return $Respose;
				}
		        	
		        else {
		        	$message='Done!';
					$codex=1;
					$status_code=200;
					$data='""';
					$Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
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
			$status_code=500;
			$data='""';
			$Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
			return $Respose;
        }
            		


	}

	public function getLikeStatusOfUserForGuide(Request $request){

        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }
            
            if (\Entrust::hasRole('traveler')){
                $guide_id=$request->input('guide_id');
                $is_liked=DB::table('guide_likes')
                ->where('guide_id',$guide_id)
                ->where('traveler_id',$user->id)
                ->first();
                if($is_liked){
                	$message='Done!';
	                $codex=1;
	                $status_code=200;
	                $data='{"is_liked":'.intval(1).'}';
	                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
	                return $Respose; 
                }else{
                	$message='Done!';
	                $codex=1;
	                $status_code=200;
	                $data='{"is_liked":'.intval(0).'}';
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
	public function getLikeStatusOfUserForGuideWithoutAuthorization(Request $request){

		try{
                $guide_id=$request->input('guide_id');
                $traveler_id=$request->input('traveler_id');
                $is_liked=DB::table('guide_likes')
                ->where('guide_id',$guide_id)
                ->where('traveler_id',$traveler_id)
                ->first();
                if($is_liked){
                	$message='Done!';
	                $codex=1;
	                $status_code=200;
	                $data='{"is_liked":'.intval(1).'}';
	                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
	                return $Respose; 
                }else{
                	$message='Done!';
	                $codex=1;
	                $status_code=200;
	                $data='{"is_liked":'.intval(0).'}';
	                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
	                return $Respose;
                }
       

        
        }catch(\Exception $ex){
            return ErrorController::error500();
        }
	}


    public function getAllItemsOfCategoryMobileApp($category_ID){
		$items = Item::where('category_ID','=',$category_ID)->where('reviewedByAdmin','=',1)
		->get();
		foreach ($items as $key => $value) {
			$items[$key]['id']=intval($items[$key]['id']);
			$items[$key]['category_ID']=intval($items[$key]['category_ID']);
			$category=Category::where('id',$items[$key]['category_ID'])->get();
			$category_name=$category[0]->category_name;
			$items[$key]['category_name']=$category_name;
			$items[$key]['latitude']=strval($items[$key]['latitude']);
			$items[$key]['longitude']=strval($items[$key]['longitude']);
			$items[$key]['no_of_views']=floatval($items[$key]['no_of_views']);
			$items[$key]['active']=floatval($items[$key]['active']);
			$items[$key]['review']=floatval($items[$key]['review']);
			$slideshowItems=SlideShowItem::where('item_ID','=',$items[$key]->id)->get();
			$slideshowItems = json_decode(json_encode($slideshowItems), True);
			foreach ($slideshowItems as $key1 => $value1) {
				$slideshowItems[$key1]['slideshow_item_ID']=$slideshowItems[$key1]['id'];
				$slideshowItems[$key1]['type']=intval($slideshowItems[$key1]['type']);
				$slideshowItems[$key1]['item_ID']=intval($slideshowItems[$key1]['item_ID']);
				$slideshowItems[$key1]['active']=intval($slideshowItems[$key1]['active']);
				$slideshowItems[$key1]['slideshow_item_ID']=intval($slideshowItems[$key1]['slideshow_item_ID']);
			}
			$items[$key]['uploads']=$slideshowItems;
		}

		foreach ($items as $key => $value) {
			$posts=Post::where('item_id','=',$items[$key]->id)->where('reviewedByAdmin','=',1)
			
			->get();
			$items[$key]['posts']=$posts;
			foreach ($posts as $key1 => $value1) {
				$posts[$key1]['item_id']=intval($posts[$key1]['item_id']);
				$posts[$key1]['active']=intval($posts[$key1]['active']);
				$posts[$key1]['author_id']=intval($posts[$key1]['author_id']);
				$author_name=User::where('id',$posts[$key1]['author_id'])->first()->name;
				$posts[$key1]['author_name']=$author_name;
				$x= strip_tags($posts[$key1]['body']);
				$posts[$key1]['post_body_for_mobile_app']=$x;

			}

		}

		foreach ($items as $key => $value) {
			$likes = DB::table('likes')
			->where('item_id','=',$items[$key]->id)
            ->select('user_id')
            ->get();
            $likes = json_decode(json_encode($likes), True);
            $array=[];
            foreach ($likes as $key1 => $value1) {
					$array[$key1]=intval($likes[$key1]['user_id']);

			}
			$items[$key]['likes']=$array;
			$items[$key]['no_of_likes']=intval(count($array));
			
		}
		$items = json_encode($items);
		$items = str_replace("\\", "", $items);


		$message='Done';
		$codex=1;
		$status_code=200;
		$data=$items;
		$Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
		return $Respose;
	}
}

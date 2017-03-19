<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Item;
use App\Post;
use App\Like;
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


    public function getAllItemsOfCategory($category_ID){

		
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
            
            if (\Entrust::hasRole('admin')){
            	$items = Item::where('category_ID','=',$category_ID)->get();
				foreach ($items as $key => $value) {
					$slideshowItems=SlideShowItem::where('item_ID','=',$items[$key]->id)->get();
					$items[$key]['uploads']=$slideshowItems;
				}
				$items = json_encode($items);
				$items = str_replace("\\", "", $items);

                $message='Done!';
                $codex=1;
                $status_code=200;
                $data=$items;//compact($count);
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


    public function setReviewedByAdminStatusToItem(Request $request){

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
            
            if (\Entrust::hasRole('admin')){

                $item_id=$request->input('item_id');
                $item_accept_decline_status=$request->input('item_accept_decline_status');

                if($item_accept_decline_status==1){
                    $item=Item::where('id',$item_id)->update(['reviewedByAdmin'=>1,'active'=>1]);
                    $message='The item was accepted!';
                    $codex=1;
                    $status_code=200;
                    $data='""';
                    $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" : '.$data.'}';
                    return $Respose;
                }else{
                    $item=Item::where('id',$item_id)->update(['reviewedByAdmin'=>1,'active'=>0]);
                    $message='The item was rejected!';
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

    public function getNotReviewedItems(){


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
            
            if (\Entrust::hasRole('admin')){

				$items = Item::where('reviewedByAdmin','=',0)->get();
				foreach ($items as $key => $value) {
					$slideshowItems=SlideShowItem::where('item_ID','=',$items[$key]->id)->get();
					$items[$key]['uploads']=$slideshowItems;
				}
				$items = json_encode($items);
				$items = str_replace("\\", "", $items);
                
                $message='Done!';
                $codex=1;
                $status_code=200;
                $data=$items;
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

	public function saveItem(Request $request){

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
            
            if (\Entrust::hasRole(['business-user','admin'])){
                
                $newItem = new Item();
        
                $newItem->item_name = $request->input('item_name');
                $newItem->category_ID = $request->input('category_id');
                $newItem->description = $request->input('description');
                $newItem->email = $request->input('email');
                $newItem->telno = $request->input('telephone');
                $newItem->fax = $request->input('fax');
                $newItem->address = $request->input('address');
                $newItem->province = $request->input('province');
                $newItem->district = $request->input('district');
                $newItem->latitude = $request->input('latitude');
                $newItem->longitude = $request->input('longitude');
                $newItem->website = $request->input('website');
                $newItem->fb = $request->input('fb');
                $newItem->gplus = $request->input('gplus');
                $newItem->twitter = $request->input('twitter');
                $newItem->thumb_image = $request->input('thumb_image');

                if(\Entrust::hasRole('admin')){
                    $newItem->reviewedByAdmin = 1;
                    $newItem->active = 1;

                }
                try{
                    if($newItem->save()){
                        $message='Item saved successfully!';
                        $codex=1;
                        $status_code=200;
                        $data="";
                        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                        return $Respose;                    
                    }else {
                        $message='Unown Error!';
                        $codex=0;
                        $status_code=500;
                        $data="";
                        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                        return $Respose;
                    }
                }catch(\Exception $ex){
                    $message='Item already exists!';
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
            $message='Unown error!';
            $codex=0;
            $status_code=500;
            $data="";//compact($count);
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
            return $Respose;
        }

    }

    public function deleteItem(Request $request){


        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }
            
            if (\Entrust::hasRole('admin')){

                $item_id=$request->input('item_id');
                $del_item=$item=Item::where('id',$item_id)->first();
                    

                if($del_item->active==1){
                    $del_item=$item=Item::where('id',$item_id)->update(['active'=>0]);
                    $message='The item was deleted successfully!';
                    $codex=1;
                    $status_code=200;
                    $data='""';
                    $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" : '.$data.'}';
                    return $Respose;
                }else{
                    $message='No such item exists, or item has been already deleted.';
                    $codex=1;
                    $status_code=404;
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

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


class UploadController extends Controller
{
	public function getAllUploads(){
		try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
            	return ErrorController::error500();
            }
            
            if (\Entrust::hasRole('admin')){

				$uploads = DB::table('slide_show_items')
					->where('reviewedByAdmin',1)
					->where('active',1)
		            ->get();

				$uploads = json_encode($uploads);
				$uploads = json_decode($uploads,true);
				foreach ($uploads as $key => $value) {
					$item_name=Item::find($uploads[$key]['item_ID'])->item_name;
					$uploads[$key]['item_name']=$item_name;
				}

				$uploads = json_encode($uploads);
				$uploads = str_replace("\\", "", $uploads);
                
                $message='Done!';
                $codex=1;
                $status_code=200;
                $data=$uploads;
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

	public function getNotReviewedUploads(){
		try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
            	return ErrorController::error500();
            }
            
            if (\Entrust::hasRole('admin')){

				$uploads = DB::table('slide_show_items')
					->where('reviewedByAdmin',0)
					->where('active',1)
		            ->get();

				$uploads = json_encode($uploads);
				$uploads = json_decode($uploads,true);
				foreach ($uploads as $key => $value) {
					$item_name=Item::find($uploads[$key]['item_ID'])->item_name;
					$uploads[$key]['item_name']=$item_name;
				}

				$uploads = json_encode($uploads);
				$uploads = str_replace("\\", "", $uploads);
                
                $message='Done!';
                $codex=1;
                $status_code=200;
                $data=$uploads;
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

	//
	public function setReviewedByAdminStatusOfUpload(Request $request){

        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
            	return ErrorController::error500();
            }
            
            if (\Entrust::hasRole('admin')){

                $upload_id=$request->input('upload_id');
                $upload_accept_decline_status=$request->input('upload_accept_decline_status');

                if($upload_accept_decline_status==1){
                    $upload=SlideShowItem::where('id',$upload_id)->update(['reviewedByAdmin'=>1,'active'=>1]);
                    $message='The upload was accepted!';
                    $codex=1;
                    $status_code=200;
                    $data='""';
                    $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" : '.$data.'}';
                    return $Respose;
                }else{
                    $upload=SlideShowItem::where('id',$upload_id)->update(['reviewedByAdmin'=>1,'active'=>0]);
                    $message='The upload was rejected!';
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
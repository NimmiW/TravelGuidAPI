<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Category;
use App\Http\Controllers\Controller;
use DB;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\ErrorController;


class CategoryController extends Controller
{
	use Helpers;

    public function getAllCategories(){


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
            
            if (\Entrust::hasRole('super-admin')){

				$categories = DB::table('categories')
		            ->groupBy('categories.id') 
		            ->get();

				$categories = json_encode($categories);
				$categories = json_decode($categories,true);
				foreach ($categories as $key => $value) {
					$cat_index=$categories[$key]['id'];
					$categories[$key]['items_url']="http://sltravelmate.com/public_html/authproject/public/index.php/portal/item/getitemswithdetails/{$cat_index}";
				}

				$categories = json_encode($categories);
				$categories = str_replace("\\", "", $categories);
                
                $message='Done!';
                $codex=1;
                $status_code=200;
                $data=$categories;
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
            $message='Unown error!';
            $codex=0;
            $status_code=500;
            $data="";//compact($count);
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
            return $Respose;
        }

	}



    public function getNotReviewedCategories(){


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
            
            if (\Entrust::hasRole('super-admin')){

				$categories = DB::table('categories')
				->where('reviewedByAdmin','=',0)
		        ->get();

				$categories = json_encode($categories);
				$categories = json_decode($categories,true);
				foreach ($categories as $key => $value) {
					$cat_index=$categories[$key]['id'];
					$categories[$key]['items_url']="http://sltravelmate.com/public_html/authproject/public/index.php/portal/item/getitemswithdetails/{$cat_index}";
				}
				$categories = json_encode($categories);
				$categories = str_replace("\\", "", $categories);
                $message='Done!';
                $codex=1;
                $status_code=200;
                $data=$categories;
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
            $message='Unown error!';
            $codex=0;
            $status_code=500;
            $data="";//compact($count);
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
            return $Respose;
        }

	}

    public function setReviewedByAdminStatusToCategory(Request $request){

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
            
            if (\Entrust::hasRole('super-admin')){

                $categoriy_id=$request->input('category_id');
                $category_accept_decline_status=$request->input('category_accept_decline_status');

                if($category_accept_decline_status==1){
                    $category=Category::where('id',$categoriy_id)->update(['reviewedByAdmin'=>1,'active'=>1]);
                    $message='The category was accepted!';
                    $codex=1;
                    $status_code=200;
                    $data='""';
                    $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" : '.$data.'}';
                    return $Respose;
                }else{
                    $category=Category::where('id',$categoriy_id)->update(['reviewedByAdmin'=>1,'active'=>0]);
                    $message='The category was rejected!';
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
            $message='Unown error!';
            $codex=0;
            $status_code=500;
            $data="";
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
            return $Respose;
        }

    }
    
    public function saveCategory(Request $request){

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
                
                $newCategory = new Category();
        
                $newCategory->category_name = $request->input('category_name');
                $newCategory->description = $request->input('description');
                $newCategory->category_picture = $request->input('category_picture');

                if(\Entrust::hasRole('super-admin')){
                    $newCategory->reviewedByAdmin = 1;
                }

                try{
                    if($newCategory->save()){
                        $message='Done!';
                        $codex=1;
                        $status_code=200;
                        $data='""';
                        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :  '.$data.'}';
                        return $Respose;
                    
                    }else{ 
                        $message='Internal error!';
                        $codex=0;
                        $status_code=500;
                        $data="";//compact($count);
                        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                        return $Respose;
                    }
                }catch(\Exception $ex){
                    $message='That category is already existing!';
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
            $data="";
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
            return $Respose;
        }

    }

    public function deleteCategory(Request $request){


        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }
            
            if (\Entrust::hasRole('super-admin')){

                $categoriy_id=$request->input('category_id');
                $del_cat=$category=Category::where('id',$categoriy_id)->first();
                //->update(['active'=>0]);
                    

                if($del_cat->active==1){
                    $del_cat=$category=Category::where('id',$categoriy_id)->update(['active'=>0]);
                    $message='The category was deleted successfully!';
                    $codex=1;
                    $status_code=200;
                    $data='""';
                    $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" : '.$data.'}';
                    return $Respose;
                }else{
                    $message='No such category exists, or category has been already deleted.';
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

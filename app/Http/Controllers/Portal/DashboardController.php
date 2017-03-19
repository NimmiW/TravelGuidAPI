<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Category;
use App\Item;
use App\Post;
use App\Http\Controllers\Controller;
use DB;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use Dingo\Api\Routing\Helpers;


class DashboardController extends Controller
{
    use Helpers;

    public function getcounts(){
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
            
            if (\Entrust::hasRole('admin')){

//reviewedByAdmin-active
//10 reviewed and rejected
//11 reviewed and accepted
//01 not reviewed

                //new category count

                $new_category_count=Category::where('reviewedByAdmin',0)
                    ->where('active',1)
                    ->count();

                //new item count

                $new_item_count=Item::where('reviewedByAdmin',0)
                    ->where('active',1)
                    ->count();

                //new post count

                $new_post_count=Post::where('reviewedByAdmin',0)
                    ->where('active',1)
                    ->count();

                $count=array('new_category_count'=>$new_category_count,
                    'new_item_count'=>$new_item_count,
                    'new_post_count'=>$new_post_count);


                $message='Done!';
                $codex=1;
                $status_code=200;
                $count = json_encode($count);
                $count = json_decode($count,true);
                $count = json_encode($count);
                $data = str_replace("\\", "", $count);
                
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
            $data="";
            $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
            return $Respose;
        }       
    }

}
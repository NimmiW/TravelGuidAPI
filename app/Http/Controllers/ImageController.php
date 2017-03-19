<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use App\SlideShowItem;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Carbon\Carbon;

class ImageController extends Controller
{
    public function upload(){
        // Path to move uploaded files
        $target_path = "uploads/";
         
        // array for final json respone
        $response = array();
         
        // getting server ip address
        $server_ip = gethostbyname(gethostname());
         
        // final file url that is being uploaded
        $file_upload_url = 'http://localhost:8000' . '/' . $target_path;
         
         
        if (isset($_FILES['image']['name'])) {
            $target_path = $target_path . basename($_FILES['image']['name']);
         
            // reading other post parameters
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            $website = isset($_POST['website']) ? $_POST['website'] : '';
         
            $response['file_name'] = basename($_FILES['image']['name']);
            $response['email'] = $email;
            $response['website'] = $website;
         
            try {
                // Throws exception incase file is not being moved
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    // make error flag true
                    $response['error'] = true;
                    $response['message'] = 'Could not move the file!';
                }
         
                // File successfully uploaded
                $response['message'] = 'File uploaded successfully!';
                $response['error'] = false;
                $response['file_path'] = $file_upload_url . basename($_FILES['image']['name']);
            } catch (Exception $e) {
                // Exception occurred. Make error flag true
                $response['error'] = true;
                $response['message'] = $e->getMessage();
            }
        } else {
            // File parameter is missing
            $response['error'] = true;
            $response['message'] = 'Not received any file!F';
        }
         
        // Echo final json response to client
        echo json_encode($response);

    }


 
   public function uploadFileByMobileApp(){

        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return ErrorController::error500();
            }
                       
            // Path to move uploaded files
            //$target_path = "uploads/";
            $target_path='uploads/items/'.$_POST['item_id'].'/';
                 
            // array for final json respone
            $response = array();
                 
            // getting server ip address
            $server_ip = gethostbyname(gethostname());
                 
            // final file url that is being uploaded
            //$file_upload_url = 'http://sltravelmate.com/public_html/authproject/public' . '/' . $target_path;
            $file_upload_url = 'http://localhost:8000' . '/' . $target_path;    
                 
            if (isset($_FILES['image']['name'])) {
                $target_path = $target_path . basename($_FILES['image']['name']);

                 
                $response['file_name'] = basename($_FILES['image']['name']);
                //bool file_exists ( string $filename )
                if(file_exists('uploads/items/'.$_POST['item_id'])){

                }else{

                    mkdir('uploads/items/'.$_POST['item_id'], 0777, true);

                }
                try {
                    // Throws exception incase file is not being moved
                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    //if (!move_uploaded_file($response['file_name'], $target_path)) {
                        // make error flag true
                        $response['error'] = true;
                        $response['message'] = 'Could not move the file!';
                        $message=$response['message'];
                        $codex=0;
                        $status_code=500;
                        $data='""';
                        $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                        return $Respose;
                    }
                 
                    // File successfully uploaded
                    $response['message'] = 'File uploaded successfully!';
                    $response['error'] = false;
                    $response['file_path'] = $file_upload_url . basename($_FILES['image']['name']);

                    //saving an entry in databse
                    $upload = new SlideShowItem();
                    $upload->item_ID=$_POST['item_id'];
                    $upload->thumb=$response['file_path'];
                    $upload->url=$response['file_path'];
                    $upload->type=$_POST['type'];

                    $upload->save();
                
                    $message=$response['message'];
                    $codex=1;
                    $status_code=200;
                    $data=$response['file_path'] ;
                    $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :"'.$data.'"}';
                    return $Respose;

                } catch (Exception $e) {
                    // Exception occurred. Make error flag true
                    $response['error'] = true;
                    $response['message'] = $e->getMessage();
           
                    $message=$response['message'];
                    $codex=0;
                    $status_code=500;
                    $data='""';
                    $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                    return $Respose;

                }
            } else {
                    // File parameter is missing
                $response['error'] = true;
                $response['message'] = 'Not received any file!F';
                $message=$response['message'];
                $codex=0;
                $status_code=400;
                $data='""';
                $Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
                return $Respose;
            }
                        

        }catch(JWTException $ex){
            return ErrorController::error401();
        }catch(\Exception $ex){
            return ErrorController::error500();
        }
       

    }

}

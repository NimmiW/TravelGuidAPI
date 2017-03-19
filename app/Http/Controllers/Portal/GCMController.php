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


class GCMController extends Controller
{
    use Helpers;

    public function sendGCM($reg_id){
        //server API key from Google APIs
        $apiKey = "AIzaSyBu9od5G17RZKL1nhPTYJlZOp4nChSGhbI";

        //client registration IDs
        $registrationIDs = array($reg_id);

        //Message to be sent
        $message = "You have received a new message";

        //Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';

        $fields = array(
            'registration_ids' => $registrationIDs,
            'data' => array( "message" => $message ),
        );
        $headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        );

        //Open connection
        $ch = curl_init();

        // Set the URL, number of POST vars, POST data
        curl_setopt( $ch, CURLOPT_URL, $url);
        curl_setopt( $ch, CURLOPT_POST, true);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields));

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $fields));

        // Execute post
        $result = curl_exec($ch);


    }



}
//data link
//https://developers.google.com/cloud-messaging/
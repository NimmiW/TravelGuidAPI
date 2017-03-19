<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class EmailController extends Controller
{

	public function resetPassword(Request $request){
        $email=$request->input('email');
        $user=DB::table('users')
            ->where('email','=',$email)->first();
        if(!$user)
            return 0;

        Mail::send('emails.test',['name'=>$user->name],function($message){
            $message->to("email@gmail.com")->subject('Sltravelmate | Password Reset Confirmation Email');
        });

        return 1;

    }
}

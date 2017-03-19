<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
use App\User;

$api = app('Dingo\Api\Routing\Router');

Route::post('/upload','ImageController@upload');

Route::get('/getcounts','Portal\AnalyticController@getcounts');
Route::get('/', function () {
    return view('welcome');
});
Route::get('/tharaka','Portal\UserController@getAllUsers');

$api->version('v1',function($api){
	
	$api->get('users/{user_id}/roles', 'App\Http\Controllers\Portal\UserController@getUserRole');
	//login for admin portal
	$api->post('auth/authenticate','App\Http\Controllers\Auth\AuthController@authenticate');
	//log in to admin portal
	$api->post('auth/authenticateforportal','App\Http\Controllers\Auth\AuthController@authenticateForPortal');
	//register to web app and mobile app
	$api->post('auth/register/clientapp','App\Http\Controllers\Auth\AuthController@registerApp');

	$api->get('auth/verifyemail','App\Http\Controllers\Auth\AuthController@verifyRegistration');
	//register to admin portal
	$api->post('auth/register/adminportal','App\Http\Controllers\Auth\AuthController@registerPortal');

	$api->get('hello2', 'App\Http\Controllers\HomeController@index');

	$api->get('/email',function(){
		Mail::send('emails.test',['name'=>'Novica'],function($message){
				$message->to('nimmirashinika@gmail.com')->subject('welcome to sltravel mate..');
		});
	});
	//send email to reset the password
	$api->post('auth/password/reset','App\Http\Controllers\Auth\PasswordController@resetPassword');
	//redirect to password reset page
	$api->get('auth/password/resetconfirm','App\Http\Controllers\Auth\PasswordController@confirmResetPassword');
	//change password after reseting
	$api->post('auth/password/change','App\Http\Controllers\Auth\PasswordController@changePassword');	
	//change the phone number
	$api->post('auth/changephonenumber','App\Http\Controllers\Auth\AuthController@changePhoneNumber');
});



$api->version('v1', ['middleware'=>'api.auth'], function($api){


	$api->get('users','App\Http\Controllers\Auth\AuthController@index');
	//get user details
	$api->get('auth/me','App\Http\Controllers\Auth\AuthController@show');
	//refresh token
	$api->get('auth/token','App\Http\Controllers\Auth\AuthController@getToken');

	$api->get('users/delete','App\Http\Controllers\Auth\AuthController@destroy');
	//logout
	$api->get('auth/logout','App\Http\Controllers\Auth\AuthController@logout');

	$api->get('/allusers', 'App\Http\Controllers\HomeController@hello');



});


//,'prefix'=>'admin'
/*$api->version('v1', ['middleware'=>'api.auth'], function($api){
	$api2->version('v1',['prefix'=>'admin'], function($api){
	
		$api2->get('/allusers', 'App\Http\Controllers\HomeController@hello');

	});
	
});*/
//*********************************************IMAGE UPLOAD and VEDIO UPLOAD******************************
$api->version('v1',function($api){
	$api->post('/uploadfilebymobile','App\Http\Controllers\ImageController@uploadFileByMobileApp');
});


//***********************************************ADMIN PORTAL*********************************************
$api->version('v1',function($api){

	//add a new admin by super admin
	$api->post('portal/users/addadmin','App\Http\Controllers\Portal\UserController@addAdmin');
	//block any user by the super admin	
	$api->post('portal/users/blockuser','App\Http\Controllers\Portal\UserController@blockUser');
	//get all categories both reviewed and not reviewed(SA)
	$api->get('portal/categories/getallcategories', 'App\Http\Controllers\Portal\CategoryController@getAllCategories');
	//get the categories which are not reviewed (SA)	
	$api->get('portal/categories/getnotreviewedcategories', 'App\Http\Controllers\Portal\CategoryController@getNotReviewedCategories');
	//delete a category(SA)
	$api->post('portal/categories/deletecategory', 'App\Http\Controllers\Portal\CategoryController@deleteCategory');
	//delete a item (SA,A)
	$api->post('portal/items/deleteitem', 'App\Http\Controllers\Portal\ItemController@deleteItem');
	//get all items belong to a particular category(A)
	$api->get('portal/items/getallitemsofcategory/{cat_id}', 'App\Http\Controllers\Portal\ItemController@getAllItemsOfCategory');
	//get the items which are not reviewed (A)	
	$api->get('portal/items/getnotrevieweditems', 'App\Http\Controllers\Portal\ItemController@getNotReviewedItems');
	//reviewing a category by superAdmin(SA)
	$api->post('portal/categories/setreviewedbyadminstatusofcategory', 'App\Http\Controllers\Portal\CategoryController@setReviewedByAdminStatusToCategory');
	//reviewing a item by superAdmin(A)
	$api->post('portal/items/setreviewedbyadminstatusofitem', 'App\Http\Controllers\Portal\ItemController@setReviewedByAdminStatusToItem');
	//reviewing a business user request by any admin(SA,A)
	$api->post('portal/users/setreviewedbyadminstatusofbu', 'App\Http\Controllers\Portal\UserController@setReviewedByAdminStatusToBU');
	//reviewing a post of a traveler by any admin(SA,A)
	$api->post('portal/posts/setreviewedbyadminstatusofpost', 'App\Http\Controllers\Portal\PostController@setReviewedByAdminStatusToPost');
	//saving a category(A,BU)
	$api->post('portal/categories/savecategory', 'App\Http\Controllers\Portal\CategoryController@saveCategory');
	//saving a item(A,BU)
	$api->post('portal/items/saveitem', 'App\Http\Controllers\Portal\ItemController@saveItem');

	//get all users(A,SA)
	$api->get('/portal/users/allusers','App\Http\Controllers\Portal\UserController@getAllUsers');
	//get roles of a user when user id is given(SA,A)
	$api->post('/portal/users/getuserroles','App\Http\Controllers\Portal\UserController@getUserRole');
	//get all admin(A,SA)
	$api->get('/portal/users/alladmins','App\Http\Controllers\Portal\UserController@getAllAdmins');
	//get all admin(A,SA)
	$api->get('/portal/users/allbusinessusers','App\Http\Controllers\Portal\UserController@getAllBusinessUsers');
	//get not reviewed buisniess users, guides, sponsors
	$api->get('/portal/users/getnotreviewedbusinessusers','App\Http\Controllers\Portal\UserController@getNotReviewedBU');
	//get all admin(A,SA)
	$api->get('/portal/users/alltravelers','App\Http\Controllers\Portal\UserController@getAllTravelers');

	//get the list of guides registered in the system
	$api->get('portal/messages/getallguides', 'App\Http\Controllers\Portal\MessageController@getGuideList');

	//get all posts for guide when guide id is given as a post parameter (ACTIVE POSTS) (A,SA,G)
	$api->post('portal/guides/getallpostsforguide', 'App\Http\Controllers\Portal\PostController@getAllPostsForGuide');
	//get all new posts for all guides which are not reviewed by admin (ACTIVE POSTS) (A,SA)
	$api->get('portal/posts/getnotreviewedposts', 'App\Http\Controllers\Portal\PostController@getNotReviewedPosts');


	//****************************dashboard details of the admin portal************************************
	//get counts of new requests including categories, items and users
	$api->get('portal/dashboard/getcount','App\Http\Controllers\Portal\DashboardController@getCounts');


	
	//***********************for guides specialized routes*************************************************		
	//get the message box basic details of a particular guide(G)
	$api->get('portal/guides/messages/getmessageboxofuser', 'App\Http\Controllers\Portal\MessageController@getMessageBoxOfUser');
	//get all the messages of a particular guide(G)
	$api->get('portal/guides/messages/getallmessagesofuser', 'App\Http\Controllers\Portal\MessageController@getAllMessagesOfUser');
	//save a message send by a particular guide to a particular traveler(G)
	$api->post('portal/guides/messages/savemessage', 'App\Http\Controllers\Portal\MessageController@saveMessage');

	$api->post('portal/guides/messages/saveseenstatus', 'App\Http\Controllers\Portal\MessageController@saveSeenStatus');
	//get messages between a particular guide and a particular traveler , traveler is authenticated
	$api->post('portal/guides/messages/getallmessagesofuserandtraveler', 'App\Http\Controllers\Portal\MessageController@getAllMessagesOfUserAndTraveler');

	$api->get('portal/sendgcm','App\Http\Controllers\Portal\GCMController@sendGCM');


	//************************Sponsorships*****************************************************************
	//get products/ membership types
	$api->get('portal/sponsorships/getsponsorship', 'App\Http\Controllers\Portal\SponsorshipController@getSponsorship');
	//transaction data save in data base
	$api->post('portal/sponsorships/success', 'App\Http\Controllers\Portal\SponsorshipController@success');
	//attach sponsorship role to a business user
	$api->post('portal/sponsorships/attachsponsorrole', 'App\Http\Controllers\Portal\SponsorshipController@attachSponsorRole');
	//update sponsor details
	$api->post('portal/sponsorships/updatemysponserdetails', 'App\Http\Controllers\Portal\SponsorshipController@updateMySponserDetails');
	//get sponsor details
	$api->get('portal/sponsorships/getmysponserdetails', 'App\Http\Controllers\Portal\SponsorshipController@getMySponserDetails');
	
	

	//**************************uploads********************************************************************
	//get all currently diplaying uploads (SA,A)
	$api->get('portal/uploads/getalluploads', 'App\Http\Controllers\Portal\UploadController@getAllUploads');
	//get not reviewed uploads by admins(SA,A)
	$api->get('portal/uploads/getnotrevieweduploads', 'App\Http\Controllers\Portal\UploadController@getNotReviewedUploads');
	//set reviewed status of a upload
	$api->post('portal/uploads/setreviewedbyadminstatusofupload', 'App\Http\Controllers\Portal\UploadController@setReviewedByAdminStatusOfUpload');	


});





//***************************************************CLIENTS*************************************************
$api->version('v1',function($api){
	//get all categories
	$api->get('client/categories/getallcategories', 'App\Http\Controllers\Client\Get\CategoryController@getAllCategories');
	//get all items belong to a particular category
	$api->get('client/items/getallitemsofcategory/{cat_id}', 'App\Http\Controllers\Client\Get\ItemController@getAllItemsOfCategory');
	//get all posts for a particular item
	$api->get('client/posts/getpostsforitem/{item_id}', 'App\Http\Controllers\Client\Post\PostController@getPostsForItem');
	//get all posts for a particular guide
	$api->get('client/posts/getpostsforguide/{guide_id}', 'App\Http\Controllers\Client\Post\PostController@getPostsForGuide');
	//get all posts for a particular guide
	$api->get('client/posts/getpostsforguide/', 'App\Http\Controllers\Client\Post\PostController@getPostsForGuide');
	//get the list of guides registered in the system
	$api->get('client/messages/getallguides', 'App\Http\Controllers\Client\Post\MessageController@getGuideList');
	//increase the number of views
	$api->post('client/items/increasenoofviews', 'App\Http\Controllers\Client\Get\ItemController@incrementNoOfViews');
	//get all items 
	$api->get('client/items/getallitems', 'App\Http\Controllers\Client\Get\ItemController@getAllItems');
	
	
	//************************Authenticated travelers have access to following routes******************************
	//get like status of looged in user for a particular item
	$api->post('client/items/getlikestatusofuserforitem', 'App\Http\Controllers\Client\Get\ItemController@getLikeStatusOfUserForItem');
	//set a like for an item
	$api->post('client/items/setlike', 'App\Http\Controllers\Client\Get\ItemController@setLike');
	//remove a like from an item
	$api->post('client/items/removelike', 'App\Http\Controllers\Client\Get\ItemController@removeLike');
	//set a like for a guide
	$api->post('client/items/setlikeforguide', 'App\Http\Controllers\Client\Get\ItemController@setLikeForGuide');
	//remove a like from a guide
	$api->post('client/items/removelikeforguide', 'App\Http\Controllers\Client\Get\ItemController@removeLikeForGuide');
	//get like status of looged in user for a particular guide
	$api->post('client/items/getlikestatusofuserforguide', 'App\Http\Controllers\Client\Get\ItemController@getLikeStatusOfUserForGuide');
	//post a post or a comment
	$api->post('client/posts/savepost', 'App\Http\Controllers\Client\Post\PostController@savePost');
	//edit a post or a comment
	$api->post('client/posts/editpost', 'App\Http\Controllers\Client\Post\PostController@editPost');
	//delete a post or a comment
	$api->post('client/posts/deletepost', 'App\Http\Controllers\Client\Post\PostController@deletePost');
	//post a post or a comment for a guide
	$api->post('client/posts/savepostforguide', 'App\Http\Controllers\Client\Post\PostController@savePostForGuide');
	//edit a post or a comment for a guide
	//edit n delete have same functions :-P so in our apps we use only client/posts/editpost and client/posts/deletepost
	$api->post('client/posts/editpostforguide', 'App\Http\Controllers\Client\Post\PostController@editPostForGuide');
	//delete a post or a comment for a guide
	$api->post('client/posts/deletepostforguide', 'App\Http\Controllers\Client\Post\PostController@deletePostForGuide');
//get all the posts which are not deleted by the user. this contain both admin reviewed and admin not-reviewed posts of that user
	$api->get('client/posts/getallpostsofuser', 'App\Http\Controllers\Client\Post\PostController@getAllPostsOfUser');

	//get the online list of guides
//****$api->get('client/messages/getonlineguides', 'App\Http\Controllers\Client\Post\MessageController@getOnlineGuideList');
	//get the message box basic details of a particular traveller
	$api->get('client/messages/getmessageboxofuser', 'App\Http\Controllers\Client\Post\MessageController@getMessageBoxOfUser');
	//get all the messages of a particular traveler
	$api->get('client/messages/getallmessagesofuser', 'App\Http\Controllers\Client\Post\MessageController@getAllMessagesOfUser');
	//save a message send by a particular traveler to a particular guide
	$api->post('client/messages/savemessage', 'App\Http\Controllers\Client\Post\MessageController@saveMessage');
	//save seen status
	$api->post('client/messages/saveseenstatus', 'App\Http\Controllers\Client\Post\MessageController@saveSeenStatus');
	//get messages between a particular guide and a particular traveler , traveler is authenticated
	$api->post('client/messages/getallmessagesofuserandguide', 'App\Http\Controllers\Client\Post\MessageController@getAllMessagesOfUserAndGuide');


	//get all sponsors details
	$api->get('client/sponsorships/getallsponsors', 'App\Http\Controllers\Client\Get\SponsorshipController@getAllSponsors');
	
});


//for IMALKA
//get like status of a user for a particular item without authorization of the user
Route::post('client/items/getlikestatusofuserforitemwithoutauthorization', 'Client\Get\ItemController@getLikeStatusOfUserForItemWithoutAuthorization');
//get like status of a user for a particular item without authorization of the user
Route::post('client/items/getlikestatusofuserforguidewithoutauthorization', 'Client\Get\ItemController@getLikeStatusOfUserForGuideWithoutAuthorization');

//imalka special
$api->version('v1',function($api){
	//get all categories
	$api->get('client/categories/getallcategoriesmobileapp', 'App\Http\Controllers\Client\Get\CategoryController@getAllCategoriesMobileApp');
	//get the list of guides registered in the system
	$api->get('client/messages/getallguidesmobileapp', 'App\Http\Controllers\Client\Post\MessageController@getGuideListMobileApp');
	//get all items belong to a particular category
	$api->get('client/items/getallitemsofcategorymobileapp/{cat_id}', 'App\Http\Controllers\Client\Get\ItemController@getAllItemsOfCategoryMobileApp');
	//save a message send by a particular traveler to a particular guide
	$api->post('client/messages/savemessagemobileapp', 'App\Http\Controllers\Client\Post\MessageController@saveMessageMobileApp');
	//get all posts for a particular guide
	$api->post('client/posts/getpostsforguidemobileapp', 'App\Http\Controllers\Client\Post\PostController@getPostsForGuideMobileApp');
			
});
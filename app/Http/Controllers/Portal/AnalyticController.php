<?php

/*namespace App\Http\Controllers\Portal;

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

use Spatie\LaravelAnalytics\LaravelAnalytics;
use Spatie\LaravelAnalytics\GoogleApiHelper;

class AnalyticController extends Controller
{
    use Helpers;

    /*public function getcounts(){

    	$x=new GoogleApiHelper();
    	//fetch the most visited pages for today and the past week
		return $x->getMostVisitedPages(365,20);

		//fetch visitors and page views for the past week
		//LaravelAnalytics::fetchVisitorsAndPageViews(Period::days(7));
    }

}





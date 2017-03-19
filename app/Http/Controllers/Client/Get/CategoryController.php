<?php

namespace App\Http\Controllers\Client\Get;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Category;

use App\Http\Controllers\Controller;

use DB;


class CategoryController extends Controller
{
    public function getAllCategories(){

    	$categoriesList = DB::table('categories')
            ->where('reviewedByAdmin','=',1)
            ->where('active',1)
            ->get();


		$categories = DB::table('categories')
            ->join('items', 'categories.id', '=', 'items.category_ID')
            ->where('categories.reviewedByAdmin','=',1)
            ->where('categories.active','=',1)
            ->where('items.reviewedByAdmin','=',1)
            ->where('items.active','=',1)
            ->select('categories.id', 'categories.category_name',DB::raw('count(*) as no_of_items'),'categories.category_picture')
            ->groupBy('categories.id') 
            ->get();

        $categoriesList = json_encode($categoriesList);
		$categoriesList = json_decode($categoriesList,true);
		$categories = json_encode($categories);
		$categories = json_decode($categories,true);
		foreach ($categoriesList as $key => $value) {
			$cat_index=$categoriesList[$key]['id'];
			$categoriesList[$key]['id']=intval($categoriesList[$key]['id']);
			$categoriesList[$key]['items_url']="http://sltravelmate.com/public_html/authproject/public/index.php/api/client/items/getallitemsofcategory/{$cat_index}";
			foreach ($categories as $key1 => $value) {
				$categoriesList[$key]['no_of_items']=intval(0);
				if($categories[$key1]['id']==$categoriesList[$key]['id']){
					$categoriesList[$key]['no_of_items']=$categories[$key1]['no_of_items'];
					break;
				}
			}
		}

		$guides=new Category();
		$guides->id=0;
		$guides->category_name="Guides";
		$guides->category_picture="https://tourceylon.files.wordpress.com/2014/09/jeep-safari-2.jpg?w=950";
		
		$guides = json_encode($guides);
		$guides = json_decode($guides,true);

		$guides['no_of_items']=DB::table('guides')
								->join('users','guides.id','=','users.id')
								->where('users.active',1)->where('users.reviewedByAdmin',1)->count();
		$guides['no_of_items']=intval($guides['no_of_items']);
		$guides['items_url']="http://sltravelmate.com/public_html/authproject/public/index.php/api/client/messages/getallguides";
		
		$categoriesList1=array();
		$categoriesList1 = json_encode($categoriesList1);
		$categoriesList1 = json_decode($categoriesList1,true);
		array_push($categoriesList1, $guides);

		foreach ($categoriesList as $key => $value) {
			$categoriesList1[$key+1]=$categoriesList[$key];
		}

		$categoriesList = json_encode($categoriesList);
		$categoriesList = str_replace("\\", "", $categoriesList);
		$categoriesList1 = json_encode($categoriesList1);
		$categoriesList1 = str_replace("\\", "", $categoriesList1);
		
		$message='Done';
		$codex=1;
		$status_code=200;
		$data=$categoriesList1;
		//$data=$categories;
		$Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
		return $Respose;
		
	}

    public function getAllCategoriesMobileApp(){

    	$categoriesList = DB::table('categories')
            ->where('reviewedByAdmin','=',1)
            ->get();


		$categories = DB::table('categories')
            ->join('items', 'categories.id', '=', 'items.category_ID')
            ->where('categories.reviewedByAdmin','=',1)
            ->where('items.reviewedByAdmin','=',1)
            ->where('items.active','=',1)
            ->select('categories.id', 'categories.category_name',DB::raw('count(*) as no_of_items'),'categories.category_picture')
            ->groupBy('categories.id') 
            ->get();

        $categoriesList = json_encode($categoriesList);
		$categoriesList = json_decode($categoriesList,true);
		$categories = json_encode($categories);
		$categories = json_decode($categories,true);
		foreach ($categoriesList as $key => $value) {
			$cat_index=$categoriesList[$key]['id'];
			$categoriesList[$key]['id']=intval($categoriesList[$key]['id']);
			$categoriesList[$key]['active']=intval($categoriesList[$key]['active']);
			$categoriesList[$key]['items_url']="http://sltravelmate.com/public_html/authproject/public/index.php/api/client/items/getallitemsofcategorymobileapp/{$cat_index}";
			foreach ($categories as $key1 => $value) {
				$categoriesList[$key]['no_of_items']=intval(0);
				if($categories[$key1]['id']==$categoriesList[$key]['id']){
					$categoriesList[$key]['no_of_items']=$categories[$key1]['no_of_items'];
					break;
				}
			}
		}

		$guides=new Category();
		$guides->id=0;
		$guides->category_name="Guides";
		$guides->category_picture="https://tourceylon.files.wordpress.com/2014/09/jeep-safari-2.jpg?w=950";
		$guides->active=intval(1);
		$guides = json_encode($guides);
		$guides = json_decode($guides,true);

		$guides['no_of_items']=DB::table('guides')
								->join('users','guides.id','=','users.id')
								->where('users.active',1)->where('users.reviewedByAdmin',1)->count();
		$guides['no_of_items']=intval($guides['no_of_items']);
		$guides['items_url']="http://sltravelmate.com/public_html/authproject/public/index.php/api/client/messages/getallguidesmobileapp";
		
		$categoriesList1=array();
		$categoriesList1 = json_encode($categoriesList1);
		$categoriesList1 = json_decode($categoriesList1,true);
		array_push($categoriesList1, $guides);

		foreach ($categoriesList as $key => $value) {
			$categoriesList1[$key+1]=$categoriesList[$key];
		}

		$sponsors=new Category();
		$sponsors->id=1000;
		$sponsors->category_name="Sponsors";
		$sponsors->category_picture="http://otoolesgac.ie/wp-content/uploads/2014/01/sponsor.jpg";
		$sponsors->active=intval(1);
		$sponsors = json_encode($sponsors);
		$sponsors = json_decode($sponsors,true);

		$sponsorCount = DB::table('users')
            ->join('sponsors','users.id','=','sponsors.id')
            ->where('reviewedByAdmin','=',1)
            ->where('active',1)
            ->count();
		$sponsors['no_of_items']=intval($sponsorCount);
		$sponsors['items_url']="http://sltravelmate.com/public_html/authproject/public/index.php/api/client/sponsorships/getallsponsors";
		

		array_push($categoriesList1, $sponsors);

		$categoriesList = json_encode($categoriesList);
		$categoriesList = str_replace("\\", "", $categoriesList);
		$categoriesList1 = json_encode($categoriesList1);
		$categoriesList1 = str_replace("\\", "", $categoriesList1);
		
		$message='Done';
		$codex=1;
		$status_code=200;
		$data=$categoriesList1;
		//$data=$categories;
		$Respose='{"message": "'.$message.'","codex":'.$codex.', "status_code":'.$status_code.',"data" :'.$data.'}';
		return $Respose;
		
	}
}

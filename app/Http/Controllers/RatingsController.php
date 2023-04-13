<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ratings;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\View\View;


class RatingsController extends Controller
{
    //

    public function addRating(Request $request){
        $userIdFK =  $request["userIdFK"];
        $ratedUserIdFK = $request["ratedUserIdFK"];
        $rating = $request["rating"];
        $delete = DB::table("ratings")->where('userIdFK', $userIdFK)->where('ratedUserIdFK', $ratedUserIdFK)->delete();
        $ratings = DB::insert("INSERT INTO ratings (userIdFK, ratedUserIdFK, rating) VALUES
        ('$userIdFK','$ratedUserIdFK','$rating')");
        return response()->json($ratings, 200);



    }


    public function ratingValue(Request $request){
        $userIdFK = $request["userIdFK"];
        $ratedUserIdFK = $request["ratedUserIdFK"];
        
        

        $rating = DB::select("SELECT * FROM `marketplace-db`.ratings WHERE userIdFK = $userIdFK AND ratedUserIdFK = $ratedUserIdFK;");
        return response()->json($rating, 200);

    }
    
}

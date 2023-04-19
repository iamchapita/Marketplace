<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class RatingsController extends Controller
{
    //

    public function setRating(Request $request)
    {
        $userIdFK =  $request["userIdFK"];
        $ratedUserIdFK = $request["ratedUserIdFK"];
        $rating = $request["rating"];
        $delete = DB::table("ratings")->where('userIdFK', $userIdFK)->where('ratedUserIdFK', $ratedUserIdFK)->delete();
        $ratings = DB::insert("INSERT INTO ratings (userIdFK, ratedUserIdFK, rating) VALUES
        ('$userIdFK','$ratedUserIdFK','$rating')");
        return response()->json($ratings, 200);
    }


    public function getRating(Request $request)
    {
        $userIdFK = $request["userIdFK"];
        $ratedUserIdFK = $request["ratedUserIdFK"];
        $rating = DB::select("SELECT * FROM `marketplace-db`.ratings WHERE userIdFK = $userIdFK AND ratedUserIdFK = $ratedUserIdFK;");
        return response()->json($rating, 200);
    }
}

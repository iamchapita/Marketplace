<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use Illuminate\Http\Request;

class MunicipalityControllers extends Controller
{
    public function index(){
        return response()->json(municipality::all());
    }
}

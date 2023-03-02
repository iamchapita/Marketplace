<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentsControllers extends Controller
{
    public function index()
    {
        return response()->json(department::all());
    }
}

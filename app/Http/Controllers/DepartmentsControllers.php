<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DepartmentsControllers extends Controller
{
    public function CreateDepartaments(Request $request){
        $departments = new Departaments;
        $departments->name = $request->input('name');
        $departments->save();

        return response()->json(['message' => 'Departamento creado', 'departments' => $departments]);
        
    }

}

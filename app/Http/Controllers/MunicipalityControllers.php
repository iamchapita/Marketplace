<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MunicipalityControllers extends Controller
{
    public function CreateMunicipality(Request $request){
        //
        $validatedData=$request->validate([
            'id' => 'required|integer|unique:municipality',
            'name' => 'required|string',
            'departmentIdFK' => 'required|integer|exists:departmentIdFK,id'
        ]);
        $municipality=new Municipality;
        $municipality->name=$validatedData['id'];
        $municipality->departmentIdFK=$validatedData['departmentIdFK'];
        $municipality->save();

        return response()->json(['message' => 'Municipio Creados', 'municipality' => $municipality]);

    }
}

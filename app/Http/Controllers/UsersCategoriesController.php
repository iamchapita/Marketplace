<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\UsersCategories;


class UsersCategoriesController extends Controller
{
    
    public function store(Request $request)
    {
        $userId = $request->get('userIdFk');
        $categorias = $request->get('categoryIdFK');
    
        // Validar que el valor de userIdFk no sea nulo
        if (empty($userId)) {
            return response()->json(['error' => 'El valor de userIdFk es nulo.'], 400);
        }
    
        $data = array_map(function ($categoryId) use ($userId) {
            return [
                'userIdFK' => $userId,
                'categoryIdFK' => $categoryId
            ];
        }, $categorias);
    
        UsersCategories::insert($data);
    
        return response()->json(['message' => 'Suscripciones guardadas exitosamente.']);
    }
    

}


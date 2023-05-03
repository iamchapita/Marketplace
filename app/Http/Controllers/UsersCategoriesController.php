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
        
        // Eliminar las categorías repetidas
        $categorias = array_unique($categorias);
        
        // Obtener las categorías que ya tiene el usuario
        $categoriasExistentes = UsersCategories::where('userIdFK', $userId)
                                                ->pluck('categoryIdFK')
                                                ->toArray();
        
        // Filtrar las categorías que ya existen
        $categoriasNuevas = array_filter($categorias, function ($categoryId) use ($categoriasExistentes) {
            return !in_array($categoryId, $categoriasExistentes);
        });
        
        // Crear los nuevos registros
        $data = array_map(function ($categoryId) use ($userId) {
            return [
                'userIdFK' => $userId,
                'categoryIdFK' => $categoryId
            ];
        }, $categoriasNuevas);
        
        UsersCategories::insert($data);
        
        return response()->json(['message' => 'Suscripciones guardadas exitosamente.']);
    }

    public function getSubscriptionCategory(Request $request)
    {
        $userId = $request->get('userIdFK');

        $categories = UsersCategories::select(
            'users_categories.userIdFK',
            'users_categories.categoryIdFK'
        )->where('users_categories.userIdFK', '=', $userId)->get();

        if ($categories->isEmpty()){
            return response()->json(['message' => 'Categorias sin Suscribir'], 500);
        } else {
            return response()->json($categories, 200);
        }
    }

}



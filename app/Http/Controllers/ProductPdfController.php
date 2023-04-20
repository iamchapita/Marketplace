<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Http\Controllers\Controller;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use PDF;


class ProductPdfController extends Controller
{
    public function generatePdf(Request $request)
    {
        // Obtener los productos con el código que proporcionaste
        $products = Product::join('users', 'users.id', '=', 'products.userIdFK')
            ->join('categories', 'categories.id', '=', 'products.categoryIdFK')
            ->join('directions', 'directions.userIdFK', '=', 'products.userIdFK')
            ->join('departments', 'departments.id', '=', 'directions.departmentIdFK')
            ->join('municipalities', 'municipalities.id', '=', 'directions.municipalityIdFK')
            ->select(
                'products.name',
                'products.description',
                'products.price',
                'products.photos',
                'categories.name as categoryName'
            )
            ->get();

        // Generar la vista con los productos
        $pdf = new Dompdf();
        $pdf = PDF::loadView('product-pdf', compact('products'));
        
        // Descargar el PDF
        return $pdf->download('prueba.pdf');
    }
}

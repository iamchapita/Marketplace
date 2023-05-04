<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Subscription;
use App\Models\UsersCategories;
use App\Http\Controllers\Controller;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDF;


class ProductPdfController extends Controller
{

    protected function base64Encode($path, $imagesToObtain)
    {

        if (!Storage::disk('public')->exists($path)) {
            return false;
        } else {
            $encodedFiles = [];
            $files = Storage::disk('public')->files($path);

            if ($imagesToObtain != 0) {
                $count = intval($imagesToObtain);
            } else {
                $count = count($files);
            }

            foreach ($files as $key => $file) {

                if ($key < $count) {
                    // Se obtiene el contenido del archivo
                    $content = Storage::disk('public')->get($file);

                    // Se obtiene el nombre del archivo
                    $name = explode('/', $file);
                    $name = $name[count($name) - 1];
                    $type = explode('.', $name);
                    $type = $type[count($type) - 1];

                    // Obteniendo el arreglo del nombre y el contenido del archivo
                    $fileReponse = array(
                        'type' => $type,
                        'name' => $name,
                        'base64Image' => base64_encode($content)
                    );

                    array_push($encodedFiles, $fileReponse);
                }
            }
            return $encodedFiles;
        }
    }

    public function generatePdf(Request $request)
    {
        // Obtener el ID del usuario autenticado
        $userId = $request->get('userIdFK');
        // Verificar si el usuario está suscrito
        $subscription = Subscription::select('subscriptionState')
            ->where('userIdFK', '=', $userId)
            ->first();

        if (!$subscription || $subscription->subscriptionState == '0') {
            return response()->json(['message' => 'El usuario no está suscrito'], 403);
        }
        // Obtener las categorías a las que el usuario está suscrito
        $categories = UsersCategories::select('categoryIdFK')
            ->where('userIdFK', '=', $userId)
            ->get()
            ->pluck('categoryIdFK')
            ->toArray();
        // Obtener los productos de las categorías a las que el usuario está suscrito
        $products = Product::join('users', 'users.id', '=', 'products.userIdFK')
            ->join('categories', 'categories.id', '=', 'products.categoryIdFK')
            ->whereIn('products.categoryIdFK', $categories)
            ->where('products.isBanned', '=', '0')
            ->where('products.isAvailable', '=', '1')
            ->select(
                'products.id',
                'products.name',
                'products.status',
                'products.price',
                'products.photos',
                'categories.name as categoryName'
            )
            ->get();

        foreach ($products as $product) {
            $product->photos = $this->base64Encode($product->photos, 1);
        }
        // Generar la vista con los productos
        $pdf = new Dompdf();
        $pdf = PDF::loadView('product-pdf', compact('products'));

        // Guardar el PDF en la carpeta de almacenamiento de Laravel
        $pdfContent = $pdf->output();
        Storage::disk('public')->put('pdf' . DIRECTORY_SEPARATOR . $userId . '_Productos_Categorias_Favoritas.pdf', $pdfContent);

        // Descargar el PDF
        return $pdf->download('Productos_Categorias_Favoritas.pdf');
    }


  



}

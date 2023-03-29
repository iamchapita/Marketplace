<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isEmpty;

class ProductController extends Controller
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

                    // Obteniendo el arreglo del nombre y el contenido del archivo
                    $fileReponse = array(
                        'name' => $name,
                        'base64Image' => base64_encode($content)
                    );

                    array_push($encodedFiles, $fileReponse);
                }
            }
            return $encodedFiles;
        }
    }

    protected function base64Decode($request, $mode = 'store')
    {

        // Ruta a guardar crear y a almacenar
        $path = 'products/' . Str::random(15);

        // Se confirma si la carpeta para el producto existe, de no ser así
        // se crea
        if (!Storage::disk('public')->exists($path)) {
            Storage::disk('public')->makeDirectory($path);
        }

        // Obteniendo el array de archivos enviados
        $images = $request->only('photos')['photos'];

        if ($images) {

            for ($i = 0; $i <  count(array_keys($images)); $i++) {

                // Obteniendo el nombre del archivo y la imagen en base64
                $imageName = $images[$i]['name'];
                $base64_image = $images[$i]['base64Image'];
                // Se almacena el archivo en la ruta
                Storage::disk('public')->put($path . '/' . $imageName, base64_decode($base64_image));
            }
            return $path;
        }

        return false;
    }

    protected function validateData($request)
    {
        // Extrayendo las llaves del arreglo de campos a validar
        $request = $request->all();
        $keys = array_keys($request);

        // Estableciendo los nombres personalizados de los atributos
        $customAttributes = array(
            $keys[0] => 'Nombre',
            $keys[1] => 'Descripción',
            $keys[2] => 'Precio',
            $keys[3] => 'Fotos',
            $keys[4] => 'Estado',
            $keys[5] => 'Usario',
            $keys[6] => 'Categoría'
        );

        // Estableciendo reglas de cada campo respectivamente
        $rules = array(
            $keys[0] => ['required', 'string', 'max:255'],
            $keys[1] => ['max:255', 'string'],
            $keys[2] => ['required', 'numeric', 'min:0'],
            $keys[3] => ['required'],
            $keys[4] => ['required', 'max:20', 'string', 'in:Usado,Nuevo'],
            $keys[5] => ['required', 'numeric'],
            $keys[6] => ['required', 'numeric']
        );

        // Mensajes personalizados para los errores
        $messages = array(
            'required' => 'El campo :attribute es requerido.',
            'min' => 'El campo :attribute está fuera de rango.',
            'max' => 'El campo :attribute está fuera de rango.',
            'unique' => 'El campo :attribute especificado ya siendo utilizado.',
            'string' => 'El campo :attribute debe ser una cadena de texto.',
            'numeric' => 'El campo :attribute debe tener 8 digitos.',
            'bewtween' => 'El campo :attribute debe estar ser 0.'
        );

        if (count($keys) > 7) {
            // Esyableciendo nombre personalizado a los campos
            $customAttributes[$keys[7]] = 'Disponible';
            $customAttributes[$keys[8]] = 'Baneado';

            // Reglas de validacion de los campos
            $rules[$keys[7]] = ['required', 'between:0,1'];
            $rules[$keys[8]] = ['required', 'between:0,1'];
        }

        // Validando los datos
        // $fields -> Campos del formulario.
        // $rules -> Reglas para validar campos.
        // $messages -> Mensajes personalizados para mostrar en caso de error.
        $validator = Validator::make($request, $rules, $messages);

        // Estableciendo los nombres de los atributos
        $validator->setAttributeNames($customAttributes);

        return $validator;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validator = $this->validateData($request);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 500);
        } else {

            // Sobreescribe el campos photos enviados desde el frontend para almacenar
            // la ruta donde se guardaron las imagenes en la BD.
            $path = $this->base64Decode($request, 'store');
            $request->merge(['photos' => $path]);

            if ($request['photos'] == false) {
                return response()->json(['error' => 'Error en las imagenes'], 500);
            } else {
                $values = $request->all();
                Product::create($values);
                return response()->json(['message' => 'Insercion Completa'], 200);
            }
        }
    }

    /*
    Obtiene las imagenes de los productos
    */
    public function getProductImages(Request $request)
    {
        $path = $request->get('path');
        $imagesToObtain = $request->has('imagesToObtain') ? $request->get('imagesToObtain') : 0;

        $path = $this->base64Encode($path, $imagesToObtain);

        if ($path == false) {
            return response()->json(['message' => 'No se han encontrado imagenes del producto'], 400);
        }

        return response()->json($path, 200);
    }

    public function getProductsWithWishlistStatus(Int $userId)
    {
        $products = DB::table('products')
            ->join('users', 'users.id', '=', 'products.userIdFK')
            ->join('categories', 'categories.id', '=', 'products.categoryIdFK')
            ->join('directions', 'directions.userIdFK', '=', 'products.userIdFK')
            ->join('departments', 'departments.id', '=', 'directions.departmentIdFK')
            ->join('municipalities', 'municipalities.id', '=', 'directions.municipalityIdFK')
            ->leftJoin('wish_lists', function ($join) use ($userId) {
                $join->on('wish_lists.productIdFK', '=', 'products.id')
                    ->where('wish_lists.userIdFK', '=', $userId);
            })
            ->select(
                'products.id',
                'products.name',
                'products.description',
                'products.price',
                'products.photos',
                'products.status',
                'products.isAvailable',
                'products.isBanned',
                'products.userIdFK',
                'products.categoryIdFK',
                DB::raw('IF(wish_lists.productIdFK IS NULL, FALSE, TRUE) as isProductInWishList'),
                'categories.name as categoryName',
                'users.firstName as userFirstName',
                'users.lastName as userLastName',
                'departments.name as departmentName',
                'municipalities.name as municipalityName'
            )
            ->orderBy('products.id', 'ASC')
            ->get();


        return response()->json($products, 200);
    }

    /**
     * Show list product.
     *
     */
    public function getProducts()
    {
        $products = Product::join('users', 'users.id', '=', 'products.userIdFK')
            ->join('categories', 'categories.id', '=', 'products.categoryIdFK')
            ->join('directions', 'directions.userIdFK', '=', 'products.userIdFK')
            ->join('departments', 'departments.id', '=', 'directions.departmentIdFK')
            ->join('municipalities', 'municipalities.id', '=', 'directions.municipalityIdFK')
            ->select(
                'products.id',
                'products.name',
                'products.description',
                'products.price',
                'products.photos',
                'products.status',
                'products.isAvailable',
                'products.isBanned',
                'products.userIdFK',
                'products.categoryIdFK',
                'categories.name as categoryName',
                'users.firstName as userFirstName',
                'users.lastName as userLastName',
                'departments.name as departmentName',
                'municipalities.name as municipalityName'
            )
            ->get();

        return response()->json($products, 200);
    }

    /**
     *
     */
    public function getProductById(Int $id)
    {

        $product = Product::join('users', 'users.id', '=', 'products.userIdFK')
            ->join('categories', 'categories.id', '=', 'products.categoryIdFK')
            ->join('directions', 'directions.userIdFK', '=', 'products.userIdFK')
            ->join('departments', 'departments.id', '=', 'directions.departmentIdFK')
            ->join('municipalities', 'municipalities.id', '=', 'directions.municipalityIdFK')
            ->select(
                'products.id',
                'products.name',
                'products.description',
                'products.price',
                'products.photos',
                'products.status',
                'products.isAvailable',
                'products.isBanned',
                'products.userIdFK',
                'products.categoryIdFK',
                'categories.name as categoryName',
                'users.firstName as userFirstName',
                'users.lastName as userLastName',
                'departments.name as departmentName',
                'municipalities.name as municipalityName'
            )
            ->find($id);

        if (is_null($product)) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        return response()->json($product, 200);
    }

    public function getProductsBySeller(Request $request){

        $sellerId = $request->get('sellerId');
        $products = Product::where('products.userIdFK', '=', $sellerId)
            ->select(
                'products.id',
                'products.name',
                'products.description',
                'products.price',
                'products.photos',
                'products.status',
                'products.isAvailable',
                'products.isBanned'
            )->get();

        if($products->isEmpty()){
            return response()->json(['message' => 'No se encontraron Productos'], 200);
        }else{
            return response()->json($products, 200);
        }
    }

    public function filterProducts(Request $request){

    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
//use Codedge\Fpdf\Fpdf\Fpdf;

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

    protected function base64Decode($request, $mode = 'store', $path = '')
    {

        if ($mode == 'store') {
            // Ruta a crear para almacenar las imagenes del producto
            $path = 'products/' . Str::random(15);
            // Se confirma si la carpeta para el producto existe, de no ser así
            // se crea
            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->makeDirectory($path);
            }
        } else {
            Storage::disk('public')->deleteDirectory($path);
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
        $request = $request->only('name', 'description', 'price', 'photos', 'status', 'userIdFK', 'categoryIdFK', 'amount');
        $keys = array_keys($request);

        // Estableciendo los nombres personalizados de los atributos
        $customAttributes = array(
            $keys[0] => 'Nombre',
            $keys[1] => 'Descripción',
            $keys[2] => 'Precio',
            $keys[3] => 'Fotos',
            $keys[4] => 'Estado',
            $keys[5] => 'Usario',
            $keys[6] => 'Categoría',
            $keys[7] => 'Cantidad',
        );

        // Estableciendo reglas de cada campo respectivamente
        $rules = array(
            $keys[0] => ['required', 'string', 'max:255'],
            $keys[1] => ['max:255', 'string'],
            $keys[2] => ['required', 'numeric', 'min:0'],
            $keys[3] => ['required'],
            $keys[4] => ['required', 'max:20', 'string', 'in:Usado,Nuevo'],
            $keys[5] => ['required', 'numeric'],
            $keys[6] => ['required', 'numeric'],
            $keys[7] => ['required', 'numeric', 'min:1'],
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

        // Validando los datos
        // $fields -> Campos del formulario.
        // $rules -> Reglas para validar campos.
        // $messages -> Mensajes personalizados para mostrar en caso de error.
        $validator = Validator::make($request, $rules, $messages);

        // Estableciendo los nombres de los atributos
        $validator->setAttributeNames($customAttributes);

        return $validator;
    }

    public function create(Request $request)
    {
        $validator = $this->validateData($request);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 500);
        } else {

            if (!$request->has('created_at')) {
                $request->merge(['created_at' => now()]);
            }

            // Sobreescribe el campos photos enviados desde el frontend para almacenar
            // la ruta donde se guardaron las imagenes en la BD.
            $path = $this->base64Decode($request, 'store');
            $request->merge(['photos' => $path]);

            if ($request['photos'] == false) {
                return response()->json(['error' => 'Error en las imagenes'], 500);
            } else {
                $values = $request->all();
                Product::insert($values);
                return response()->json(['message' => 'Insercion Completa'], 200);
            }
        }
    }

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
                'products.created_at as createdAt',
                DB::raw('IF(wish_lists.productIdFK IS NULL, FALSE, TRUE) as isProductInWishList'),
                'categories.name as categoryName',
                'users.firstName as userFirstName',
                'users.lastName as userLastName',
                'departments.name as departmentName',
                'municipalities.name as municipalityName'
            )
            ->where('products.isAvailable', '=', 1)
            ->where('products.isBanned', '=', 0)
            ->orderBy('products.id', 'ASC')
            ->get();

        if ($products->isEmpty()) {
            return response()->json(['message' => 'No se encontraron Productos'], 500);
        } else {
            return response()->json($products, 200);
        }
    }

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
                'products.wasSold',
                'products.userIdFK',
                'products.categoryIdFK',
                'products.created_at as createdAt',
                'categories.name as categoryName',
                'users.firstName as userFirstName',
                'users.lastName as userLastName',
                'departments.name as departmentName',
                'municipalities.name as municipalityName'
            )->where('products.isAvailable', '=', 1)
            ->where('products.isBanned', '=', 0)
            ->get();

        if ($products->isEmpty()) {
            return response()->json(['message' => 'No se encontraron Productos'], 500);
        } else {
            return response()->json($products, 200);
        }
    }

    public function getPopularProducts()
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
                'products.wasSold',
                'products.userIdFK',
                'products.categoryIdFK',
                'products.created_at as createdAt',
                'categories.name as categoryName',
                'users.firstName as userFirstName',
                'users.lastName as userLastName',
                'departments.name as departmentName',
                'municipalities.name as municipalityName'
            )->where('products.isAvailable', '=', 1)
            ->where('products.isBanned', '=', 0)
            ->where('products.views', '>=', 1)
            ->orderBy('products.views', 'DESC')
            ->get();

        if ($products->isEmpty()) {
            return response()->json(['message' => 'No se encontraron Productos'], 500);
        } else {
            return response()->json($products, 200);
        }
    }

    public function orderByProducts(Request $request)
    {
        if (!$request->has('attribute')) {
            return response()->json(['message' => 'No se recibió el atributo criterio para ordenar.'], 500);
        }

        if (!$request->has('type')) {
            return response()->json(['message' => 'No se recibió el tipo de ordenamiento.'], 500);
        }

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
                'products.wasSold',
                'products.userIdFK',
                'products.categoryIdFK',
                'products.created_at as createdAt',
                'categories.name as categoryName',
                'users.firstName as userFirstName',
                'users.lastName as userLastName',
                'departments.name as departmentName',
                'municipalities.name as municipalityName'
            )
            ->orderBy($request->get('attribute'), $request->get('type'))
            ->get();

        if ($products->isEmpty()) {
            return response()->json(['message' => 'No se encontraron Productos'], 500);
        } else {
            return response()->json($products, 200);
        }
    }

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
                'products.amount',
                'products.isAvailable',
                'products.isBanned',
                'products.wasSold',
                'products.userIdFK',
                'products.views',
                'products.categoryIdFK',
                'categories.name as categoryName',
                'users.id as userId',
                'users.firstName as userFirstName',
                'users.lastName as userLastName',
                'departments.name as departmentName',
                'municipalities.name as municipalityName'
            )
            ->find($id);

        if (is_null($product)) {
            return response()->json(['message' => 'Producto no encontrado'], 500);
        } else {
            $product->views += 1;
            $product->save();
            return response()->json($product, 200);
        }
    }

    public function getProductsBySeller(Request $request)
    {

        $whereConditions = [
            ['users.id', '=', $request->get('sellerId')]
        ];

        if ($request->has('isBannedStatus')) {
            array_push($whereConditions, ['products.isBanned', '=', $request->get('isBannedStatus') ? 1 : 0]);
        }

        if ($request->has('isAvailableStatus')) {
            array_push($whereConditions, ['products.isAvailable', '=', $request->get('isAvailableStatus') ? 1 : 0]);
        }

        $products = Product::join('users', 'users.id', '=', 'products.userIdFK')
            ->join('categories', 'categories.id', '=', 'products.categoryIdFK')
            ->join('directions', 'directions.userIdFK', '=', 'products.userIdFK')
            ->join('departments', 'departments.id', '=', 'directions.departmentIdFK')
            ->join('municipalities', 'municipalities.id', '=', 'directions.municipalityIdFK')
            ->where($whereConditions)
            ->select(
                'products.id',
                'products.name',
                'products.description',
                'products.price',
                'products.photos',
                'products.status',
                'products.amount',
                'products.isAvailable',
                'products.wasSold',
                'products.isBanned',
                'products.created_at as createdAt',
                'products.updated_at as updatedAt',
                'categories.name as categoryName',
                'users.firstName as userFirstName',
                'users.lastName as userLastName',
                'users.email as userEmail',
                'users.isBanned as userIsBanned',
                'departments.name as departmentName',
                'municipalities.name as municipalityName'
            )
            ->get();

        if ($products->isEmpty()) {
            return response()->json(['message' => 'No se encontraron Productos'], 500);
        } else {
            return response()->json($products, 200);
        }
    }

    public function update(Request $request)
    {
        //Para buscar por id los porductos
        $product = Product::find($request->get('id'));

        if (is_null($product)) {
            return response()->json(['message' => 'Producto no encontrado'], 500);
        } else {
            $validator = $this->validateData($request);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 500);
            } else {

                // Sobreescribe el campos photos enviados desde el frontend para almacenar
                // la ruta donde se guardaron las imagenes en la BD.
                $path = $this->base64Decode($request, 'update', $product->photos);
                $request->merge(['photos' => $path]);

                if ($request['photos'] == false) {
                    return response()->json(['error' => 'Error en las imagenes'], 500);
                } else {

                    //Actualizar Productos
                    $product->name = $request->input('name');
                    $product->description = $request->input('description');
                    $product->price = $request->input('price');
                    $product->photos = $request->input('photos');
                    $product->status = $request->input('status');
                    $product->amount = $request->input('amount');
                    $product->userIdFK = $request->input('userIdFK');
                    $product->categoryIdFK = $request->input('categoryIdFK');
                    $product->updated_at = now();

                    // Se queda comentado por si se necesita a futuro

                    // $product->isAvailable = $request->input('isAvailable') ? $request->input('isAvailable') : $product->isAvailable;
                    // $product->wasSold = $request->input('wasSold') ? $request->input('wasSold') : $product->wasSold;
                    // $product->isBanned = $request->input('isBanned') ? $request->input('isBanned') : $product->isBanned;

                    $product->save();
                    return response()->json(['message' => 'Actualización Completa.'], 200);
                }
            }
        }
    }

    public function getProductst(Request $request)
    {
        $id = $request["id"];
        $page = $request["page"];
        $category = $request["category"];
        $department = $request["department"];
        $id = "%" . $id . "%";
        $category = "%" . $category . "%";
        $department = "%" . $department . "%";

        if ($request['category'] == "todos"  || $request['category'] == 0 ||  $request['category'] == '0') {
            $category = "%%";
        }
        if ($request['department'] == "todos" || $request['department'] == 0 ||  $request['department'] == '0') {
            $department = "%%";
        }


        if (intval($page) == 1) {
            $ini = 0;
            $fin = 8;
        } else {
            $fin = 8 * $page;
            $ini = $fin - 8;
        }
        if ($request['pricemin'] == 0 && $request['pricemax'] == 0) {
            $products = DB::select("SELECT p.id,
            p.name,
            p.description,
            p.price,
            p.photos,
            p.status ,
            p.isAvailable,
            p.isBanned ,
            p.userIdFk,
            c.name  categoryName ,
            de.name departmentName,
            u.firstName userFirstName,
            u.lastName  userLastName,
            m.name municipalityName,
            IF(w.productIdFK IS NULL, FALSE, TRUE) isProductInWishList
            FROM `marketplace-db`.products p
            INNER JOIN `marketplace-db`.categories c ON p.categoryIdFK  = c.id
            INNER JOIN  `marketplace-db`.users u ON p.userIdFK = u.id
            INNER JOIN `marketplace-db`.directions d ON u.id = d.userIdFK
            INNER JOIN `marketplace-db`.departments de ON de.id = d.departmentIdFK
            INNER JOIN `marketplace-db`.municipalities m ON m.id = d.municipalityIdFK
            LEFT JOIN `marketplace-db`.wish_lists w ON w.userIdFK = '$id'
            WHERE c.id LIKE '$category' AND de.id LIKE '$department'
            AND p.isBanned = 0 AND p.isAvailable = 1
            ORDER BY p.created_at DESC
            ;");
            return response()->json($products, 200);
        } else {
            $pricemin = intval($request['pricemin']);
            $pricemax = intval($request['pricemax']);

            $products = DB::select("SELECT p.id,
            p.name,
            p.description,
            p.price,
            p.photos,
            p.status ,
            p.isAvailable,
            p.isBanned ,
            p.userIdFk,
            c.name  categoryName ,
            de.name departmentName,
            u.firstName userFirstName,
            u.lastName  userLastName,
            m.name municipalityName,
            IF(w.productIdFK IS NULL, FALSE, TRUE) isProductInWishList
            FROM `marketplace-db`.products p
            INNER JOIN `marketplace-db`.categories c ON p.categoryIdFK  = c.id
            INNER JOIN  `marketplace-db`.users u ON p.userIdFK = u.id
            INNER JOIN `marketplace-db`.directions d ON u.id = d.userIdFK
            INNER JOIN `marketplace-db`.departments de ON de.id = d.departmentIdFK
            INNER JOIN `marketplace-db`.municipalities m ON m.id = d.municipalityIdFK
            LEFT JOIN `marketplace-db`.wish_lists w ON w.userIdFK = '$id'
            WHERE c.id LIKE '$category' AND de.id LIKE '$department' AND p.isBanned = 0 AND p.isAvailable = 1 AND p.price  between $pricemin AND $pricemax
            ORDER BY p.created_at DESC
            ;");
            return response()->json($products, 200);
        }
    }

    public function setWasSoldValue(Request $request)
    {
        $product =  Product::find($request->get('id'));

        if (is_null($product)) {
            return response()->json(['message' => 'Producto no encontrado'], 500);
        } else {
            $product->wasSold = intval($request->get('wasSold'));
            $product->save();
            return response()->json(['message' => 'Se actualizó el estado del Producto'], 200);
        }
    }

    public function setIsAvailable(Request $request)
    {
        $product =  Product::find($request->get('id'));

        if (is_null($product)) {
            return response()->json(['message' => 'Producto no encontrado'], 500);
        } else {
            $product->isAvailable = intval($request->get('isAvailable'));
            $product->save();
            return response()->json(['message' => 'Se actualizó el estado del Producto'], 200);
        }
    }

    public function setIsBanned(Request $request)
    {
        $product =  Product::find($request->get('id'));

        if (is_null($product)) {
            return response()->json(['message' => 'Producto no encontrado'], 500);
        } else {
            $product->isBanned = intval($request->get('isBanned'));
            $product->save();
            return response()->json(['message' => 'Se actualizó el estado del Producto'], 200);
        }
    }

    public function buscaproduct(Request $request)
    {
        $name = $request->query('name');
        $consulta = DB::table('products')
            ->when($name, function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            ->select('*')
            ->get();

        return response()->json($consulta, 200);
    }

    public function getProductsStatistics()
    {
        $productsStatistics = Product::select(
            DB::raw('COUNT(*) AS total')
        )->first();

        return response()->json($productsStatistics, 200);
    }

    public function getAllProducts($registersPerPage = null, $page = null)
    {
        $productFields = [
            'id',
            'name',
            'price',
            'status',
            'amount',
            DB::raw('IF(isAvailable = 1, "Público", "Privado") as isAvailable'),
            DB::raw('IF(wasSold = 0, "Disponible", "Vendido") as wasSold'),
            DB::raw('IF(isBanned = 0, "No Banneado", "Banneado") as isBanned'),
            'created_at as createdAt',
        ];

        if (!$page) {
            $products = Product::select(...$productFields)
                ->paginate(intval($registersPerPage));
        } else {
            $products = Product::select(...$productFields)
                ->skip(($page - 1) * $registersPerPage)
                ->take($registersPerPage)
                ->get();
        }

        if ($products->isEmpty()) {
            return response()->json(['message' => 'No se encontraron productos.'], 500);
        } else {
            return response()->json($products, 200);
        }
    }

    public function getProductosVendido()
    {
        $results = DB::table('products as p')
            ->select('p.name', DB::raw('SUM(p.amount) as Total_ProductosVendidos'), 'd.name as departamento')
            ->join('users as u', 'u.id', '=', 'p.userIdFK')
            ->join('directions as di', 'di.userIdFK', '=', 'u.id')
            ->join('departments as d', 'd.id', '=', 'di.departmentIdFK')
            ->where('p.wasSold', 1)
            ->groupBy('p.name', 'd.name')
            ->orderByDesc('Total_ProductosVendidos')
            ->get();

        return $results;
    }

    public function getProductsInsertadosMes(){
        $results = DB::table('products')
        ->select(
        DB::raw("DATE_FORMAT(products.created_at, '%Y-%m-%d') AS fecha_creacion"),
        DB::raw("MONTHNAME(products.created_at) AS nombre_mes"),
        DB::raw("MONTH(products.created_at) AS numero_mes"),
        DB::raw("DAY(products.created_at) AS dia"),
        DB::raw("YEAR(products.created_at) AS anio"),
        'products.name AS Nombre'
        )
        ->orderByRaw('MONTH(products.created_at)')
        ->get();
    
        return $results;
    
    }
}

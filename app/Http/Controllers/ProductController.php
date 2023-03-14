<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{

    protected function base64Encode(Int $id)
    {

        // Ruta de guardado de imagenes
        $path = 'products/' . $id;

        if (!Storage::disk('public')->exists($path)) {
            return response()->json(['message' => 'No existen Imágenes de este Producto'], 400);
        } else {
            $encodedFiles = [];
            $files = Storage::disk('public')->files($path);

            foreach($files as $file){
                // Se obtiene el contenido del archivo
                $content = Storage::disk('public')->get($file);

                // Se obtiene el nombre del archivo
                $name = explode('/', $file);
                $name = $name[count($name)-1];

                // Obteniendo el arreglo del nombre y el contenido del archivo
                $fileReponse = array(
                    'name' => $name,
                    'base64Image' => base64_encode($content)
                );

                array_push($encodedFiles, $fileReponse);
            }

            return response()->json($encodedFiles, 200);
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
            return response()->json(['error' => $validator->errors()]);
        } else {

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

    /**
     * Show list product.
     *
     */
    public function getProduct()
    {
        return response()->json(product::all());
    }

    /**
     *
     */
    public function getProductById(Int $id)
    {
        $product = product::find($id);
        if (is_null($product)) {
            return response()->json(['message' => 'Producto no encontrado']);
        }
        return response()->json([$product, 200]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(Product $product)
    // {
    //
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {

        //Para buscar por id los porductos
        $product = Product::findOrFail($id);

        $validator = $this->validateData($request);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        } else {
            //Actualizar Productos
            $product->name = $request->input('name');
            $product->description = $request->input('description');
            $product->price = $request->input('price');
            $product->photos = $request->input('photos');
            $product->isAvailable = $request->input('isAvailable');
            $product->isBanned = $request->input('isBanned');
            $product->userIdFK = $request->input('userIdFK');
            $product->categoryIdFK = $request->input('categoryIdFK');
            $product->save();
        }


        // Redirigir a la lista de productos con un mensaje de éxito
        return response()->json(['message' => ''], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}

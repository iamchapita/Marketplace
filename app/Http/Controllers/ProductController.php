<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{

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
            $keys[4] => 'Estado'
        );

        // Estableciendo reglas de cada campo respectivamente
        $rules = array(
            $keys[0] => ['required', 'string', 'max:255', 'regex:/([\w \,\+\-\/\#\$\(\)]+)/'],
            $keys[1] => ['max:255', 'string', 'regex:/([a-zA-Z \.\(\)0-9 \, \:\-\+\=\!\$\%\&\*\?\"\"\{\}\n\<\>\?\¿]+)/'],
            $keys[2] => ['required', 'numeric', 'min:0', 'regex:/(0\.((0[1-9]{1})|([1-9]{1}([0-9]{1})?)))|(([1-9]+[0-9]*)(\.([0-9]{1,2}))?)/'],
            $keys[3] => ['required', 'string'],
            $keys[4] => ['required', 'max:20', 'string', 'in:Usado,Nuevo']
        );

        // Mensajes personalizados para los errores
        $messages = array(
            'required' => 'El campo :attribute es requerido.',
            'min' => 'El campo :attribute está fuera de rango.',
            'max' => 'El campo :attribute está fuera de rango.',
            'unique' => 'El campo :attribute especificado ya siendo utilizado.',
            'string' => 'El campo :attribute debe ser una cadena de texto.',
            'numeric' => 'El campo :attribute debe tener 8 digitos.',
            'bewtween' => 'El campo :attribute debe estar ser 0 '
        );

        if (count($keys) > 5) {

            // Esyableciendo nombre personalizado a los campos
            $$customAttributes[$keys[5]] = 'Disponible';
            $$customAttributes[$keys[6]] = 'Baneado';
            $$customAttributes[$keys[7]] = 'Usario';
            $$customAttributes[$keys[8]] = 'Categoría';

            // Reglas de validacion de los campos
            $rules[$keys[5]] = ['required', 'between:0,1'];
            $rules[$keys[6]] = ['required', 'between:0,1'];
            $rules[$keys[7]] = ['required', 'min:0', 'numeric'];
            $rules[$keys[8]] = ['required', 'min:0', 'numeric'];
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

            $values = $request->all();
            DB::table('products')->insert($values);

            return response()->json(['success' => 'true']);
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
    public function getProductId($id)
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

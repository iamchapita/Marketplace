<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    function validateData($request){
        // Extrayendo las llaves del arreglo de campos a validar
        $keys= array_keys($request);

        // Estableciendo los nombres personalizados de los atributos
        $customAttributes = array(
            $keys[0] =>'name',
            $keys[1] =>'description',
            $keys[2] =>'price',
            $keys[3] =>'photos',
            $keys[4] =>'status',
            $keys[5] =>'isAvailable',
            $keys[6] =>'isBanned',
            $keys[7] =>'userIdFK',
            $keys[8] =>'categoryIdFK'
        );

         // Estableciendo reglas de cada campo respectivamente
         $rules = array(
            $keys[0] => ['required', 'string', 'max:80','regex:/^[a-zA-Z]+$/'],
            $keys[1] => ['required', 'string', 'max:80'],
            $keys[3] => ['required', 'numeric', 'max:80','min:0','max:15','regex:/^[1-9][0-9]*(?:\.[0-9]{2})?$/'],
            $keys[4] => ['required', 'array', 'max:80'],
            $keys[5] => ['required', 'array', 'max:80'],
            $keys[6] => ['required', 'array', 'max:80'],
            $keys[7] => ['required', 'string', 'max:80'],
            //Verificar que tipo de variable es bigInteger
            $keys[8] => ['required', 'bigInteger', 'max:80'],
         );
      
         $messages = array(
            'required' => 'El campo :attribute es requerido.',
            'min' => 'El campo :attribute está fuera de rango.',
            'max' => 'El campo :attribute está fuera de rango.',
            'string' => 'El campo :attribute debe ser una cadena de texto.',
            'numeric'=> 'El Campo : El atributo debe ser solo numeros.',
            'bigInteger'=> 'El Campo : El atributo solo acepta datos especificos.'
        );

        $validator = Validator::make($request, $rules, $messages);


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
        $validator = $this->validateData($request->all());

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $producto = new Producto();
    $producto->name = $request->name;
    $producto->description = $request->description;
    $producto->price = $request->price;
    $producto->photos = $request->photos;
    $producto->status = $request->status;
    $producto->isAvailable = $request->isAvailable;
    $producto->isBanned = $request->isBanned;
    $producto->userIdFK = $request->userIdFK;
    $producto->categoryIdFK = $request->categoryIdFK;
    $producto->save();

    // Redireccionar al usuario a la página de éxito
    return redirect('/exito');

    }
    
    /**
     * Show list product.
     * 
     */
    public function getProduct(){
      return response()->json(product::all(),200); 
      
    }
 
    /**
     * 
     */
    public function getProductId($id){
       $product=product::find($id);
       if(is_null($product)){
        return response()->json(['Mensaje'=>'Producto no encontrado'],404);
       }
      return response()->json($product::find($id),200);
    }

    /**
     * Edit products
     */
    public function editProduct(Request $request, $id){
        
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
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}

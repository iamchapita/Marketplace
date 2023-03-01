<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
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
    public function create()
    {
        //
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
        //Para buscar por id los porductos 
        $product = Product::findOrFail($id);
        //Hacer las diferentes validaciones
        $validator = Validator::make($request->all(), [
        'name' => 'required|max:255|regex:/^[a-zA-Z]+$/',
        'description' => 'required|max:255',
        'price' => 'required|numeric|min:0|regex:/^[1-9][0-9]*(?:\.[0-9]{2})?$/',
        'photos' => 'required|max:255',
        'isAvailable' => 'required|numeric|min:0',
        'isBanned' => 'required|numeric|min:0',
        'userIdFK' => 'required|numeric|min:0',
        'categoryIdFK' => 'required|max:255',
        ]);
        // Si la validación falla, redirigir de vuelta al formulario con los 
        //errores
        if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
        }
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
        // Redirigir a la lista de productos con un mensaje de éxito
        return redirect('/products')->with('success', 'El producto ha sido 
        actualizado correctamente.');
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

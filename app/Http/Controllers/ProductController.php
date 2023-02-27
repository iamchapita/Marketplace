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
        $product = Products::find($id);
        $product->name = $request->name; //price userIdFK
        $product->price = $request->price;
        $product->userIdFK = $request->userIdFK;
    
        $product->save();
    
        return response()->json(['message' => 'Producto actualizado']);
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

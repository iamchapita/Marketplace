<?php

namespace App\Http\Controllers;

use App\Models\WishList;
use Illuminate\Http\Request;

class WishListController extends Controller
{
    
    /**
     *  lista productos en la lista de deseos.
     */
    public function index($id)
    {
       /* $wishlist = Auth::user()->wishlist;
        $products = $wishlist->products;
    findall
        return response()->json(['data' => $products]);*/

        return response()->json(WishList::find( $id,'userIdFK'));
    }
    

    /**
     * guardar un producto en la lista a la vez.
     */
    public function store(Request $request)
    {
        $productId=$request->input('product_id');
        $userId=auth()->user()->id;

            // Busca si ya existe una lista de deseos para el usuario actual
    $wishList = WishList::where('userIdFK', $userId)->first();
    if (!$wishList) {
        // Si no existe, crea una nueva lista de deseos para el usuario actual
        $wishList = new WishList();
        $wishList->userIdFK = $userId;
        $wishList->addedDate = now();
        $wishList->save();
    }
    // Busca si el producto ya está en la lista de deseos del usuario actual
    $productWishList = ProductWishList::where('product_id', $productId)->where('wish_list_id', $wishList->id)->first();
    if (!$productWishList) {
        // Si el producto no está en la lista de deseos, lo agrega
        $productWishList = new ProductWishList();
        $productWishList->product_id = $productId;
        $productWishList->wish_list_id = $wishList->id;
        $productWishList->save();
    }

    return response()->json(['success' => true]);
    }

    

    /**
     * eliminar un producto de la lista.
     */
    public function delete(Product $product)
    {
        Auth::user()->wishlist->products()->detach($product);
    
        return response()->json([], 204);
    }
    

/******************************************************************/
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

   
    

    /**
     * Display the specified resource.
     */
    public function show(WishList $wishList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WishList $wishList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WishList $wishList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WishList $wishList)
    {
        //
    }
}

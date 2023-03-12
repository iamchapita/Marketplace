<?php

namespace App\Http\Controllers;

use App\Models\WishList;
use Illuminate\Http\Request;

class WishListController extends Controller
{
    
    /**
     *  lista productos en la lista de deseos.
     */
    public function index()
    {
        $wishlist = Auth::user()->wishlist;
        $products = $wishlist->products;

        return view('wishlist.index', compact('products'));
    }

    /**
     * guardar un producto en la lista a la vez.
     */
    public function store(Product $product)
    {
        Auth::user()->wishlist->products()->attach($product);
    
        return redirect()->route('wishlist.index');
    }

    /**
     * eliminar un producto de la lista.
     */
    public function delete(Product $product)
    {
    Auth::user()->wishlist->products()->detach($product);

    return redirect()->route('wishlist.index');
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

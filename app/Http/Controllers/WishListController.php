<?php

namespace App\Http\Controllers;

use App\Models\WishList;
use Illuminate\Http\Request;

class WishListController extends Controller
{

    /**
     *  Obtencion de todos las listas de deseos.
     */
    public function index()
    {
        $wishlist = WishList::all();
        return response()->json([$wishlist], 200);
    }


    /**
     * guardar un producto en la lista a la vez.
     */
    public function store(Request $request)
    {
        $request->merge(['addedDate' => date('Y-m-d')]);
        $values =  $request->all();
        WishList::insert($values);

        return response()->json(['success' => true], 200);
    }



    /**
     * eliminar un producto de la lista.
     */
    public function delete(Request $request)
    {
        $id = $request->only('id');

        WishList::destroy($id);

        return response()->json(['message' => 'Success'], 200);
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

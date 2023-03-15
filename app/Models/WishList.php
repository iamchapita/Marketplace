<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishList extends Model
{
    use HasFactory;
    public function products()
    {
        //Relacion de la clase Producto
        return $this->belongsToMany(Product::class);
    }
}

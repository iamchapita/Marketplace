<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    // Sobreescritura del nombre de la Tabla en la BD
    /*
        Laravel por defecto agrega una 's' al final del nombre de la tabla
        se utiliza esta variable evitar que Laravel agregue la 's'.
    */
    protected $table = 'ProductCategory';
}

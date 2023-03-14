<?php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
        });

        // Lista de categorias
        $categories = [
            ['name' => "Electrónica"],
            ['name' => "Moda y accesorios"],
            ['name' => "Hogar y jardín"],
            ['name' => "Deportes y actividades al aire libre"],
            ['name' => "Salud y belleza"],
            ['name' => "Juguetes y juegos"],
            ['name' => "Mascotas"],
            ['name' => "Automóviles y motocicletas"],
            ['name' => "Alimentos y bebidas"],
            ['name' => "Libros, música y películas"],
            ['name' => "Muebles y decoración"],
            ['name' => "Viajes y turismo"],
            ['name' => "Suministros de oficina y escolares"],
            ['name' => "Instrumentos musicales"],
            ['name' => "Artesanías y manualidades"],
            ['name' => "Joyería y relojes"],
            ['name' => "Cuidado del hogar"],
            ['name' => "Equipamiento empresarial"],
            ['name' => "Suministros para fiestas y eventos"],
            ['name' => "Tecnología y accesorios"],
            ['name' => "Bebés y niños"],
            ['name' => "Productos de limpieza"],
            ['name' => "Suministros médicos"],
            ['name' => "Arte y coleccionables"],
            ['name' => "Suministros para mascotas"],
            ['name' => "Suministros para deportes"],
            ['name' => "Suministros para actividades al aire libre"],
            ['name' => "Herramientas y mejoras para el hogar"],
            ['name' => "Equipo de oficina"],
            ['name' => "Suministros para la industria de alimentos y bebidas"],
            ['name' => "Suministros para la industria de la construcción"]
        ];

        // Insercion en la base de datos
        Category::insert($categories);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};

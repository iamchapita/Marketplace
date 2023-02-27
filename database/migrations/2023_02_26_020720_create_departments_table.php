<?php

use App\Models\Department;
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
        Schema::create('departments', function (Blueprint $table) {
            $table->tinyInteger('id')->autoIncrement()->unsigned();
            $table->string('name', 50);
        });

        DB::table('departments')->insert(
            [
                ['name' => 'Atlantida'],
                ['name' => 'Colón'],
                ['name' => 'Comayagua'],
                ['name' => 'Copán'],
                ['name' => 'Cortés'],
                ['name' => 'Choluteca'],
                ['name' => 'El Paraíso'],
                ['name' => 'Francisco Morazán'],
                ['name' => 'Gracias a Dios'],
                ['name' => 'Intibucá'],
                ['name' => 'Islas de la Bahía'],
                ['name' => 'La Paz'],
                ['name' => 'Lempira'],
                ['name' => 'Ocotepeque'],
                ['name' => 'Olancho'],
                ['name' => 'Santa Bárbara'],
                ['name' => 'Valle'],
                ['name' => 'Yoro'],
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};

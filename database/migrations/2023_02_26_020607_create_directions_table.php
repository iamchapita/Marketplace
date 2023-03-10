<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Direction;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('directions', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('departmentIdFK')->unsigned();
            $table->smallInteger('municipalityIdFK')->unsigned();
            $table->string('description')->nullable();
            $table->bigInteger('userIdFK')->unsigned();
            $table->timestamps();
        });

        Direction::create([
            'departmentIdFK' => '01',
            'municipalityIdFK' => '01',
            'userIdFK' => '1'
        ]);

        Direction::create([
            'departmentIdFK' => '01',
            'municipalityIdFK' => '01',
            'userIdFK' => '2'
        ]);

        Direction::create([
            'departmentIdFK' => '01',
            'municipalityIdFK' => '01',
            'userIdFK' => '3'
        ]);

        Direction::create([
            'departmentIdFK' => '01',
            'municipalityIdFK' => '01',
            'userIdFK' => '4'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('directions');
    }
};

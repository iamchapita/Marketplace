<?php

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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('description', 250);
            $table->double('price', null, null, true);
            $table->string('photos', 100);
            $table->string('status', 20);
            $table->boolean('isAvailable')->default(true);
            $table->boolean('isBanned')->default(false);
            $table->bigInteger('userIdFK')->nullable(false)->unsigned();
            $table->bigInteger('categoryIdFK')->nullable(false)->unsigned();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

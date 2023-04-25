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
            $table->integer('amount', false, true)->default(1);
            $table->integer('views', false, true)->default(0)->nullable(false);
            $table->boolean('isAvailable')->default(true);
            $table->boolean('wasSold')->default(false);
            $table->boolean('isBanned')->default(false);
            $table->bigInteger('userIdFK')->nullable(false)->unsigned();
            $table->bigInteger('categoryIdFK')->nullable(false)->unsigned();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
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

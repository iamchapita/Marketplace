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
        Schema::create('Direction', function (Blueprint $table) {
            $table->id();
            $table->string('department', 100);
            $table->string('municipality', 100);
            $table->string('description');
            $table->bigInteger('userIdFK')->nullable(false)->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Direction');
    }
};

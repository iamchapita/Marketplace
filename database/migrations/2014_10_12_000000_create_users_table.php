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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstName', 80);
            $table->string('lastName', 80);
            $table->string('dni',20);
            $table->string('email')->unique();
            $table->string('phoneNumber')->unique();
            $table->timestamp('birthDate');
            $table->string('password');
            $table->boolean('isAdmin')->default(0);
            $table->boolean('isClient')->default(1);
            $table->boolean('isSeller')->default(0);
            $table->double('raiting')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->rememberToken()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

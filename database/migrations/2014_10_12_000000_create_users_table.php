<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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
            $table->string('dni', 15)->unique();
            $table->string('email')->unique();
            $table->string('phoneNumber');
            $table->timestamp('birthDate');
            $table->string('password');
            $table->boolean('isAdmin')->default(0);
            $table->boolean('isClient')->default(1);
            $table->boolean('isSeller')->default(0);
            $table->boolean('isBanned')->default(0);
            $table->boolean('isEnabled')->default(1);
            $table->double('raiting')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->rememberToken()->nullable();
        });

        // Creacion de usuario Administrator
        User::create([
            'firstName' => 'admin',
            'lastName' => 'admin',
            'dni' => '0000-0000-00000',
            'email' => 'admin@noexiste.com',
            'phoneNumber' => '00000000',
            'birthDate' => '2023-01-01',
            'password' => Hash::make('Admin1234*'),
            'isAdmin' => '1',
            'isClient' => '0',
            'isSeller' => '0',
            'isBanned' => '0',
            'isEnabled' => '1'
        ]);

        // Creacion de usuario Cliente
        User::create([
            'firstName' => 'Cliente',
            'lastName' => 'Cliente',
            'dni' => '0000-0000-00001',
            'email' => 'clienteestrella@noexiste.com',
            'phoneNumber' => '00000000',
            'birthDate' => '2023-01-01',
            'password' => Hash::make('Cliente1234*'),
            'isAdmin' => '0',
            'isClient' => '1',
            'isSeller' => '0',
            'isBanned' => '0',
            'isEnabled' => '1'
        ]);

        // Creacion de usuario Vendedor
        User::create([
            'firstName' => 'Vendedor',
            'lastName' => 'Vendedor',
            'dni' => '0000-0000-00002',
            'email' => 'vendedorestrella@noexiste.com',
            'phoneNumber' => '00000000',
            'birthDate' => '2023-01-01',
            'password' => Hash::make('Vendedor1234*'),
            'isAdmin' => '0',
            'isClient' => '1',
            'isSeller' => '1',
            'isBanned' => '0',
            'isEnabled' => '1'
        ]);

        // Creacion de usuario Banneado
        User::create([
            'firstName' => 'Banneado',
            'lastName' => 'Banneado',
            'dni' => '0000-0000-00003',
            'email' => 'banneado@noexiste.com',
            'phoneNumber' => '00000000',
            'birthDate' => '2023-01-01',
            'password' => Hash::make('Banneado1234*'),
            'isAdmin' => '0',
            'isClient' => '1',
            'isSeller' => '1',
            'isBanned' => '1',
            'isEnabled' => '1'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

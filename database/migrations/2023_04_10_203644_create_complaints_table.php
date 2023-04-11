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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('userIdFK')->nullable(false)->unsigned();
            $table->bigInteger('userIdReported')->nullable(false)->unsigned();
            $table->string('description')->nullable(false);
            $table->string('evidences', 100)->nullable(false);
            $table->boolean('isAwaitingResponse')->nullable(false)->default(true);
            $table->boolean('wasApproved')->nullable(false);
            $table->timestamps('created_at')->nullable()->useCurrent();
            $table->timestamps('updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};

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
        Schema::create('asignacion_personals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("asignacion_detalle_id");
            $table->unsignedBigInteger("personal_id");
            $table->timestamps();

            $table->foreign("asignacion_detalle_id")->on("asignacion_detalles")->references("id");
            $table->foreign("personal_id")->on("personals")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_personals');
    }
};

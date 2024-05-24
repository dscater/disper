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
        Schema::create('asignacion_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("asignacion_id");
            $table->unsignedBigInteger("lugar_id");
            $table->unsignedBigInteger("detalle_entrenamiento_id")->nullable();
            $table->string("lat");
            $table->string("lng");
            $table->date("fecha");
            $table->time("hora")->nullable();
            $table->integer("requerido")->nullable();
            $table->integer("total_personal")->nullable();
            $table->integer("restante")->nullable();
            $table->timestamps();

            $table->foreign("asignacion_id")->on("asignacions")->references("id");
            $table->foreign("lugar_id")->on("lugars")->references("id");
            $table->foreign("detalle_entrenamiento_id")->on("detalle_entrenamientos")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_detalles');
    }
};

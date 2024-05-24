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
        Schema::create('detalle_entrenamientos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("entrenamiento_id");
            $table->unsignedBigInteger("lugar_id");
            $table->integer("ocupados");
            $table->date("fecha");
            $table->time("hora");
            $table->timestamps();

            $table->foreign("entrenamiento_id")->on("entrenamientos")->references("id");
            $table->foreign("lugar_id")->on("lugars")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_entrenamientos');
    }
};

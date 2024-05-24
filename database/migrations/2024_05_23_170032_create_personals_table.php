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
        Schema::create('personals', function (Blueprint $table) {
            $table->id();
            $table->string("nombre");
            $table->string("paterno")->nullable();
            $table->string("materno")->nullable();
            $table->string("ci");
            $table->string("ci_exp");
            $table->string("estado_civil");
            $table->string("genero");
            $table->string("dir")->nullable();
            $table->string("correo")->nullable();
            $table->string("cel")->nullable();
            $table->string("nombre_contacto")->nullable();
            $table->string("cel_contacto")->nullable();
            $table->string("foto")->nullable();
            $table->string("estado");
            $table->date("fecha_registro")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personals');
    }
};

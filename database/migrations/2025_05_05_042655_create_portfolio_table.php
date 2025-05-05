<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('portfolio', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(); // Título opcional para la imagen
            $table->string('image_path'); // Ruta de la imagen
            $table->text('description')->nullable(); // Descripción opcional
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('portfolio');
    }
};

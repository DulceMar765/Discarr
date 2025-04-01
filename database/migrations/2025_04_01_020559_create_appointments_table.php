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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('calendar_day_id')->constrained()->onDelete('cascade');
            $table->string('time_slot'); // Ejemplo: '10:00-11:00'
            $table->enum('status', ['confirmed', 'pending', 'canceled', 'completed'])
                  ->default('pending');
            $table->timestamps();
            // Índice compuesto para evitar reservas duplicadas
            $table->unique(['calendar_day_id', 'time_slot']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};

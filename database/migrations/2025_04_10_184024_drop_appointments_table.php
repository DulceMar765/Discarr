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
        Schema::dropIfExists('appointments'); // Elimina la tabla si existe
    }

    public function down()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('calendar_day_id');
            $table->string('time_slot');
            $table->enum('status', ['confirmed', 'pending', 'canceled', 'completed'])->default('pending');
            $table->timestamps();
        });
    }
};

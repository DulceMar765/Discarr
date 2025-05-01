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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('token', 64)->unique()->nullable();
            
            // Añadimos también otros campos básicos si no existen ya
            if (!Schema::hasColumn('projects', 'name')) {
                $table->string('name')->nullable();
            }
            if (!Schema::hasColumn('projects', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('projects', 'status')) {
                $table->string('status')->default('pendiente');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('token');
        });
    }
};

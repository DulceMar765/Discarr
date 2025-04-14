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
        Schema::create('project_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade'); // Relación con proyectos
            $table->foreignId('supplier_id')->nullable()->constrained(); // Proveedor (opcional)
            $table->decimal('amount', 10, 2); // Ej: 1500.50
            $table->string('description');
            $table->date('date');
            $table->enum('type', ['material', 'labor', 'logistics', 'other']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_costs');
    }
};
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
        Schema::create('medical_facility_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('medical_facilities')->onDelete('cascade');
            $table->string('name');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->string('specialization')->nullable();
            $table->enum('status', ['Open', 'Closed'])->default('Open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_facility_units');
    }
};

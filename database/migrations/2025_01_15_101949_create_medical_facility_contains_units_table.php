<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('medical_facility_contains_unit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_facility_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('medical_facility_units')->onDelete('cascade');
            $table->timestamps();
            // Using a shorter custom name for the unique index
            $table->unique(['medical_facility_id', 'unit_id'], 'facility_unit_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_facility_contains_unit');
    }
};
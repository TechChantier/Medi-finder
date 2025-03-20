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
        Schema::create('medical_facilities', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->text('address');
            $table->text('description');
            $table->string('emergency_contact', 255)->nullable()->unique();
            $table->text('google_map_url')->nullable()->unique();
            $table->text('operating_hours')->nullable();
            $table->enum('category', ['Health Care', 'Pharmacy', 'Clinic'])->default('Health Care');
            $table->json('units');
            $table->enum('status', ['Open', 'Closed'])->default('Open');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_facilities');
    }
};

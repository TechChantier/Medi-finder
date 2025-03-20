<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            $table->text('emergency_contact')->nullable();
            $table->text('google_map_url')->nullable();
            $table->text('operating_hours')->nullable();
            $table->enum('category', ['Health Care', 'Pharmacy', 'Clinic'])->default('Health Care');
            $table->json('units');
            $table->enum('status', ['Open', 'Closed'])->default('Open');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Add unique indexes with prefix lengths
        DB::statement('ALTER TABLE medical_facilities ADD UNIQUE INDEX medical_facilities_emergency_contact_unique (emergency_contact(191))');
        DB::statement('ALTER TABLE medical_facilities ADD UNIQUE INDEX medical_facilities_google_map_url_unique (google_map_url(191))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_facilities');
    }
};

// use Illuminate\Database\Migrations\Migration;


// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up(): void
//     {
//         Schema::create('medical_facilities', function (Blueprint $table) {
//             $table->id();

//             $table->unsignedBigInteger('user_id');
//             $table->text('address');
//             $table->text('description');
//             $table->string('emergency_contact', 255)->nullable()->unique();
//             $table->string('google_map_url')->nullable()->unique();
//             $table->text('operating_hours')->nullable();
//             $table->enum('category', ['Health Care', 'Pharmacy', 'Clinic'])->default('Health Care');
//             $table->json('units');
//             $table->enum('status', ['Open', 'Closed'])->default('Open');

//             $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
//             $table->timestamps();
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('medical_facilities');
//     }
// };

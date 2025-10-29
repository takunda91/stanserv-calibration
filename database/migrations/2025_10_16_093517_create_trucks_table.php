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
        Schema::create('trucks', function (Blueprint $table) {
            $table->id();
            $table->string('horse_reg')->unique();
            $table->string('trailer_reg')->unique();
            // truck details
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->integer('year')->nullable();
            $table->string('horse_chassis_number')->nullable();
            $table->string('engine_number')->nullable();
            $table->string('trailer_chassis_number')->nullable();
            $table->string('road_license_number')->nullable();
            // owner details
            $table->string('owner_name')->nullable();
            $table->string('owner_address')->nullable();
            $table->string('owner_phone')->nullable();
            $table->string('owner_cell')->nullable();
            $table->string('owner_email')->nullable();
            $table->string('owner_contact_name')->nullable();
            $table->string('owner_contact_phone')->nullable();
            $table->string('owner_driver_name')->nullable();
            $table->string('owner_driver_phone')->nullable();
            // truck config
            $table->string('truck_type')->nullable();
            $table->string('tank_shape')->nullable();
            $table->string('truck_suspension_type')->nullable();
            $table->string('air_bags')->nullable();
            // meta data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trucks');
    }
};

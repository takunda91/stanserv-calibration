<?php

use App\Models\Calibration;
use App\Models\User;
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
        Schema::create('calibration_reading_interpolations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Calibration::class);
            $table->integer('compartment_number');
            $table->integer('dip_mm');
            $table->integer('volume');
            $table->foreignIdFor(User::class, 'run_by' );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calibration_reading_interpolations');
    }
};

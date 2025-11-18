<?php

use App\Models\CalibrationReading;
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
        Schema::table('calibration_reading_interpolations', static function (Blueprint $table) {
            $table->foreignIdFor(CalibrationReading::class)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interpolations', function (Blueprint $table) {
            //
        });
    }
};

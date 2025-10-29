<?php

use App\Models\Calibration;
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
        Schema::create('calibration_compartments', function (Blueprint $table) {
            $table->id();
            $table->integer('number');
            $table->foreignIdFor(Calibration::class)->constrained();
            $table->decimal('starting_volume')->default(0);
            $table->decimal('capacity')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calibration_compartments');
    }
};

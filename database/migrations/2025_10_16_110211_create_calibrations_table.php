<?php

use App\Models\Truck;
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
        Schema::create('calibrations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Truck::class)->constrained()->cascadeOnDelete();
            $table->string('calibration_number')->unique()->comment('Becomes certificate number');
            $table->string('calibrate_using');
            $table->string('status');
            $table->integer('number_of_compartments');
            $table->decimal('compartment_starting_volume', 8, 2)->nullable();
            $table->decimal('compartment_capacity')->nullable();
            $table->string('aborted_reason')->nullable();
            $table->date('abort_date')->nullable();
            $table->json('permit_to_work')->nullable();
            $table->json('risk_assessment')->nullable();
            $table->json('precheck')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calibrations');
    }
};

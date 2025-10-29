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
        Schema::table('calibrations', function (Blueprint $table) {
            $table->date('calibration_date')->nullable()->default(now());
            $table->integer('coupling_height_before')->nullable();
            $table->integer('coupling_height_after')->nullable();
            $table->json('meta')->nullable();
            $table->json('sign_off_list')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

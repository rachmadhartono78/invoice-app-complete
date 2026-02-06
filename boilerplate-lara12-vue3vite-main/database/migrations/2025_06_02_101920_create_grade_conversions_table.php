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
        Schema::create('grade_conversions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('scale');
            $table->unsignedInteger('min_score');
            $table->unsignedInteger('max_score');
            $table->string('grade');
            $table->string('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_conversions');
    }
};

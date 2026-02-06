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
        Schema::table('organizations', function (Blueprint $table) {
            $table->decimal('longitude', 10, 8)->nullable()->after('address');
            $table->decimal('latitude', 10, 8)->nullable()->after('longitude');
            $table->boolean('is_active')->default(true)->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['longitude', 'latitude', 'is_active']);
        });
    }
};

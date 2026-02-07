<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Index for LIKE search on product name
            $table->index('name');
            
            // Index for LIKE search on product code
            $table->index('code');
            
            // Composite index for category filter + active status
            $table->index(['category_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['code']);
            $table->dropIndex(['category_id', 'is_active']);
        });
    }
};

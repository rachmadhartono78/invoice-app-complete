<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::table('item_categories', function (Blueprint $table) {
            // Make slug nullable so existing records don't break
            if (!Schema::hasColumn('item_categories', 'slug')) {
                $table->string('slug')->nullable()->after('name');
            }
            else {
                $table->string('slug')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('item_categories', function (Blueprint $table) {
            if (Schema::hasColumn('item_categories', 'slug')) {
                $table->dropColumn('slug');
            }
        });
    }
};

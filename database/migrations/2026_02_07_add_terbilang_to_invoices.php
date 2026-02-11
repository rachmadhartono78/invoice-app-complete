<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('invoices', function (Blueprint $table) {
            // Add terbilang field if it doesn't exist
            if (!Schema::hasColumn('invoices', 'terbilang')) {
                $table->text('terbilang')->nullable()->after('notes');
            }
        });
    }

    public function down() {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'terbilang')) {
                $table->dropColumn('terbilang');
            }
        });
    }
};

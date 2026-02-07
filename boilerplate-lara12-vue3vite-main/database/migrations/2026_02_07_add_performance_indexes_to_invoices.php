<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Index untuk ORDER BY invoice_date DESC (paling sering digunakan)
            if (!Schema::hasIndex('invoices', 'invoices_invoice_date_index')) {
                $table->index('invoice_date');
            }
            
            // Composite index untuk filtered queries: status + created_by
            if (!Schema::hasIndex('invoices', 'invoices_status_created_by_index')) {
                $table->index(['status', 'created_by']);
            }
            
            // Index untuk invoice_number search (LIKE queries)
            if (!Schema::hasIndex('invoices', 'invoices_invoice_number_index')) {
                $table->fullText('invoice_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['invoice_date']);
            $table->dropIndex(['status', 'created_by']);
            $table->dropFullText(['invoice_number']);
        });
    }
};

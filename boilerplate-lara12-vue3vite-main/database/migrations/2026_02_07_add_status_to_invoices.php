<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Check if status column exists, if not, add it
            if (!Schema::hasColumn('invoices', 'status')) {
                $table->enum('status', ['DRAFT', 'QUOTED', 'INVOICED', 'PARTIAL_PAID', 'PAID', 'VOID'])->default('DRAFT')->after('total');
            }
            
            // Quotation reference
            if (!Schema::hasColumn('invoices', 'quotation_number')) {
                $table->string('quotation_number')->nullable()->after('status');
            }
            if (!Schema::hasColumn('invoices', 'quotation_date')) {
                $table->date('quotation_date')->nullable()->after('quotation_number');
            }
            
            // Audit trail
            if (!Schema::hasColumn('invoices', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('quotation_date');
            }
            if (!Schema::hasColumn('invoices', 'quoted_by')) {
                $table->unsignedBigInteger('quoted_by')->nullable()->after('created_by');
            }
            if (!Schema::hasColumn('invoices', 'quoted_at')) {
                $table->timestamp('quoted_at')->nullable()->after('quoted_by');
            }
            if (!Schema::hasColumn('invoices', 'invoiced_by')) {
                $table->unsignedBigInteger('invoiced_by')->nullable()->after('quoted_at');
            }
            if (!Schema::hasColumn('invoices', 'invoiced_at')) {
                $table->timestamp('invoiced_at')->nullable()->after('invoiced_by');
            }
            if (!Schema::hasColumn('invoices', 'paid_by')) {
                $table->unsignedBigInteger('paid_by')->nullable()->after('invoiced_at');
            }
            if (!Schema::hasColumn('invoices', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('paid_by');
            }
            
            // Payment tracking
            if (!Schema::hasColumn('invoices', 'dp_amount')) {
                $table->decimal('dp_amount', 15, 2)->default(0)->after('paid_at')->comment('Down payment amount');
            }
            if (!Schema::hasColumn('invoices', 'paid_amount')) {
                $table->decimal('paid_amount', 15, 2)->default(0)->after('dp_amount')->comment('Total amount paid');
            }
            
            // Project reference
            if (!Schema::hasColumn('invoices', 'project_name')) {
                $table->string('project_name')->nullable()->after('paid_amount');
            }
            if (!Schema::hasColumn('invoices', 'delivery_phase')) {
                $table->string('delivery_phase')->nullable()->after('project_name')->comment('Phase 1, Phase 2, etc');
            }
            
            // Additional notes
            if (!Schema::hasColumn('invoices', 'internal_notes')) {
                $table->text('internal_notes')->nullable()->after('delivery_phase');
            }
            
            // Indexes for quick lookup (check if not exists)
            if (!Schema::hasIndex('invoices', 'invoices_status_index')) {
                $table->index('status');
            }
            if (!Schema::hasIndex('invoices', 'invoices_quotation_number_index')) {
                $table->index('quotation_number');
            }
            if (!Schema::hasIndex('invoices', 'invoices_project_name_index')) {
                $table->index('project_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['quotation_number']);
            $table->dropIndex(['project_name']);
            $table->dropColumn([
                'status', 'quotation_number', 'quotation_date',
                'created_by', 'quoted_by', 'quoted_at', 'invoiced_by', 'invoiced_at',
                'paid_by', 'paid_at', 'dp_amount', 'paid_amount', 'project_name',
                'delivery_phase', 'internal_notes'
            ]);
        });
    }
};

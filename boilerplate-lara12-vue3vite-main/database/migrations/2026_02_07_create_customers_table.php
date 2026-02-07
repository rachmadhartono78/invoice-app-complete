<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Customer code/ID');
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('tax_id')->nullable()->comment('NPWP');
            $table->enum('payment_terms', ['cash', 'net_7', 'net_14', 'net_30', 'net_45', 'net_60'])->default('net_30');
            $table->decimal('credit_limit', 15, 2)->default(0)->comment('Credit limit for this customer');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('name');
            $table->index('is_active');
        });

        // Add customer_id to invoices table
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->after('invoice_date')->constrained('customers')->onDelete('restrict');
                $table->index('customer_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
        
        Schema::dropIfExists('customers');
    }
};

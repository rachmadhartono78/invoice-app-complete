<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->string('payment_number')->unique()->comment('Payment receipt number');
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'giro', 'credit_card', 'other'])->default('bank_transfer');
            $table->string('reference_number')->nullable()->comment('Bank ref, check number, etc');
            $table->text('notes')->nullable();
            $table->string('receipt_file')->nullable()->comment('Payment receipt/proof file path');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            $table->index('invoice_id');
            $table->index('payment_date');
            $table->index('payment_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

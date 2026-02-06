<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // Master categories
        Schema::create('item_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Master items
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('item_categories')->onDelete('set null');
            $table->string('name')->unique();
            $table->string('code')->nullable()->unique();
            $table->string('unit')->default('pcs'); // pcs, set, box, dll
            $table->decimal('price', 15, 2)->default(0); // Harga standar
            $table->integer('quantity')->default(0); // Qty standar dari CSV
            $table->string('area')->nullable(); // Area penyimpanan
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('category_id');
            $table->index('is_active');
        });

        // Add item_id reference to invoice_items (opsional, untuk tracking)
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('set null')->after('id');
        });
    }

    public function down() {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropForeignIdFor('items');
            $table->dropColumn('item_id');
        });

        Schema::dropIfExists('items');
        Schema::dropIfExists('item_categories');
    }
};

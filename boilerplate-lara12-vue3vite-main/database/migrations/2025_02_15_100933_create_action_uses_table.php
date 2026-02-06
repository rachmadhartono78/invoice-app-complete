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
        Schema::create('action_uses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('menu_authority_id')->nullable()->constrained('menu_authorities')->onDelete('cascade');
            $table->foreignUuid('action_id')->nullable()->constrained('actions')->onDelete('cascade');
            $table->boolean('value')->dafault(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_uses');
    }
};

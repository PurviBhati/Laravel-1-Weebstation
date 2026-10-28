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
        Schema::table('variants', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropUnique(['product_id', 'size_id']);
            $table->dropColumn(['product_id', 'stock']);
        });

        Schema::table('variants', function (Blueprint $table) {
            $table->unique(['size_id', 'value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('variants', function (Blueprint $table) {
            $table->dropUnique(['size_id', 'value']);
            $table->foreignId('product_id')->nullable()->constrained('product')->cascadeOnDelete();
            $table->unsignedBigInteger('stock')->default(0);

        });
    }
};

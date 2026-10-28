<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('category_variants', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->constrained('category')
                ->cascadeOnDelete();

            $table->foreignId('variant_id')
                ->constrained('variants')
                ->cascadeOnDelete();

            $table->unique(['category_id', 'variant_id']);
        });
    }

    public function down(): void
    {
        Schema::table('category_variants', function (Blueprint $table) {
            $table->dropUnique(['category_id', 'variant_id']);
            $table->dropConstrainedForeignId('variant_id');
            $table->dropConstrainedForeignId('category_id');
        });
    }
};

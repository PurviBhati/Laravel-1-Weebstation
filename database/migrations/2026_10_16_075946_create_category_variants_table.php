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
        Schema::create('category_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('category')
                ->cascadeOnDelete();

            $table->foreignId('variant_id')
                ->constrained('variants')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['category_id', 'variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_variants');
    }
};

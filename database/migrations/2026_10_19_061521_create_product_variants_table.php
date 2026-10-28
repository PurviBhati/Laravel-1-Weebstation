
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id');

            $table->unsignedInteger('stock')->default(0);
            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on('product')
                ->cascadeOnDelete();

            $table->foreign('variant_id')
                ->references('id')
                ->on('variants')
                ->cascadeOnDelete();

            $table->unique(['product_id', 'variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};

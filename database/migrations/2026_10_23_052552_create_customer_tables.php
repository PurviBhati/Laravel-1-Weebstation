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
            $table->string('name');
            $table->string('phone', 15)->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('pincode', 10)->nullable();
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('product')->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('variants')->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->timestamps();
        });

        Schema::create('wishlist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('product')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['customer_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlist_items');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('customers');
    }
};

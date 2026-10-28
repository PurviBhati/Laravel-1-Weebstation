<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_number')->unique();
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();

                $table->string('customer_name');
                $table->string('customer_email')->nullable();
                $table->string('customer_phone', 20);

                $table->text('address');
                $table->string('city');
                $table->string('pincode', 10);

                $table->decimal('subtotal', 10, 2);
                $table->decimal('total', 10, 2);

                $table->string('payment_method')->default('cod');      // cod, online_demo
                $table->string('payment_status')->default('pending');  // pending, paid, failed
                $table->string('status')->default('pending');          // pending, confirmed, shipped, delivered, cancelled
                $table->timestamp('paid_at')->nullable();

                $table->text('notes')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('orders', function (Blueprint $table) {
                if (! Schema::hasColumn('orders', 'customer_id')) {
                    $table->foreignId('customer_id')->nullable()->after('order_number')
                        ->constrained('customers')->nullOnDelete();
                }

                if (! Schema::hasColumn('orders', 'paid_at')) {
                    $table->timestamp('paid_at')->nullable()->after('status');
                }
            });

            // Email is optional at checkout.
            Schema::table('orders', function (Blueprint $table) {
                $table->string('customer_email')->nullable()->change();
            });
        }

        if (! Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();

                $table->foreignId('product_id')->nullable()->constrained('product')->nullOnDelete();
                $table->foreignId('variant_id')->nullable()->constrained('variants')->nullOnDelete();

                // Saved copy, so old orders never change
                $table->string('product_name');
                $table->string('size')->nullable();
                $table->decimal('price', 10, 2);
                $table->unsignedInteger('quantity');
                $table->decimal('line_total', 10, 2);

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};

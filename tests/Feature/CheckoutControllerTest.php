<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CheckoutControllerTest extends TestCase
{
    public function test_checkout_page_renders_totals_for_a_customer_cart(): void
    {
        $customer = $this->createCustomerWithCart();

        $response = $this->actingAs($customer, 'customer')
            ->get(route('checkout.index'));

        $response->assertOk();
        $response->assertSee('Your order');
        $response->assertSee('₹355.00');
    }

    public function test_placing_order_saves_shipping_and_gst_in_the_total(): void
    {
        $customer = $this->createCustomerWithCart();

        $response = $this->actingAs($customer, 'customer')
            ->post(route('checkout.place'), [
                'customer_name' => 'Test Customer',
                'customer_phone' => '9876543210',
                'customer_email' => 'customer@example.com',
                'address' => '1 Example Street',
                'city' => 'Mumbai',
                'pincode' => '400001',
                'payment_method' => 'cod',
            ]);

        $response->assertRedirect('/user/orders/1');
        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'subtotal' => 100,
            'shipping_charge' => 250,
            'gst_rate' => 5,
            'gst_amount' => 5,
            'total' => 355,
        ]);
        $this->assertDatabaseHas('product', ['id' => 1, 'stock' => 4]);
        $this->assertDatabaseCount('cart_items', 0);
    }

    private function createCustomerWithCart(): Customer
    {
        $this->createCheckoutTables();

        $customer = Customer::query()->create([
            'name' => 'Test Customer',
            'phone' => null,
            'email' => null,
        ]);

        $product = Product::query()->create([
            'name' => 'Test Product',
            'price' => 100,
            'stock' => 5,
            'status' => 'active',
        ]);

        CartItem::query()->create([
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'variant_id' => null,
            'quantity' => 1,
        ]);

        return $customer;
    }

    private function createCheckoutTables(): void
    {
        Schema::create('customers', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('phone', 15)->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('pincode', 10)->nullable();
            $table->timestamps();
        });

        Schema::create('product', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->string('status')->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('product_images', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('path');
        });

        Schema::create('variants', function (Blueprint $table): void {
            $table->id();
            $table->string('value');
            $table->unsignedBigInteger('size_id')->nullable();
        });

        Schema::create('product_variants', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id');
            $table->unsignedInteger('stock')->default(0);
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->unsignedInteger('quantity');
            $table->timestamps();
        });

        Schema::create('wishlist_items', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('product_id');
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->string('order_number')->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone', 20);
            $table->text('address');
            $table->string('city');
            $table->string('pincode', 10);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_charge', 10, 2)->default(0);
            $table->decimal('gst_rate', 5, 2)->default(0);
            $table->decimal('gst_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('payment_method')->default('cod');
            $table->string('payment_status')->default('pending');
            $table->string('status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->string('product_name');
            $table->string('size')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('line_total', 10, 2);
            $table->timestamps();
        });
    }
}

<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Order;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminOrderControllerTest extends TestCase
{
    public function test_admin_order_index_counts_statuses_and_filters_unseen_address_changes(): void
    {
        $this->createOrderTables();
        $this->createOrder('ORD-UNSEEN', 'pending', [
            'previous_address' => 'Old address',
            'address_updated_at' => now()->subMinutes(10),
        ]);
        $this->createOrder('ORD-SEEN', 'confirmed', [
            'previous_address' => 'Older address',
            'address_updated_at' => now()->subMinutes(20),
            'address_seen_at' => now()->subMinutes(10),
        ]);
        $this->actingAs($this->admin(), 'web');

        $response = $this->get(route('admin.orders.index'));

        $response->assertOk();
        $response->assertSee('ORD-UNSEEN');
        $response->assertSee('ORD-SEEN');
        $response->assertSee('Pending');
        $response->assertSee('Confirmed');

        $response = $this->get(route('admin.orders.index', ['status' => 'address']));

        $response->assertOk();
        $response->assertSee('ORD-UNSEEN');
        $response->assertDontSee('ORD-SEEN');
    }

    public function test_admin_can_render_and_mark_an_unseen_address_change_as_seen(): void
    {
        $this->createOrderTables();
        $order = $this->createOrder('ORD-ADDRESS', 'pending', [
            'previous_address' => 'Old address',
            'address_updated_at' => now()->subMinute(),
        ]);
        $this->actingAs($this->admin(), 'web');

        $response = $this->get(route('admin.orders.show', $order));

        $response->assertOk();
        $response->assertSee('Address updated');
        $response->assertSee('Previous address: Old address');
        $this->assertTrue($order->hasUnseenAddressChange());

        $response = $this->post(route('admin.orders.address-seen', $order));

        $response->assertRedirect(route('admin.orders.show', $order));
        $this->assertNotNull($order->fresh()->address_seen_at);
        $this->assertFalse($order->fresh()->hasUnseenAddressChange());
    }

    private function admin(): AdminUser
    {
        $admin = new AdminUser(['name' => 'Test Admin']);
        $admin->setAttribute('id', 1);

        return $admin;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createOrder(string $orderNumber, string $status, array $attributes = []): Order
    {
        return Order::query()->create(array_merge([
            'order_number' => $orderNumber,
            'customer_name' => 'Test Customer',
            'customer_phone' => '9876543210',
            'address' => 'Current address',
            'city' => 'Mumbai',
            'pincode' => '400001',
            'subtotal' => 100,
            'shipping_charge' => 0,
            'gst_rate' => 5,
            'gst_amount' => 5,
            'total' => 105,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'status' => $status,
        ], $attributes));
    }

    private function createOrderTables(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->string('order_number')->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone');
            $table->text('address');
            $table->string('city');
            $table->string('pincode');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_charge', 10, 2)->default(0);
            $table->decimal('gst_rate', 5, 2)->default(0);
            $table->decimal('gst_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('payment_method');
            $table->string('payment_status');
            $table->string('status');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancelled_by')->nullable();
            $table->timestamp('address_updated_at')->nullable();
            $table->timestamp('address_seen_at')->nullable();
            $table->text('previous_address')->nullable();
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

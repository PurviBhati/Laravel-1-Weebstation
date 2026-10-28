<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'shipping_charge')) {
                $table->decimal('shipping_charge', 10, 2)->default(0)->after('subtotal');
            }

            if (! Schema::hasColumn('orders', 'gst_rate')) {
                $table->decimal('gst_rate', 5, 2)->default(0)->after('shipping_charge');
            }

            if (! Schema::hasColumn('orders', 'gst_amount')) {
                $table->decimal('gst_amount', 10, 2)->default(0)->after('gst_rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_charge', 'gst_rate', 'gst_amount']);
        });
    }
};

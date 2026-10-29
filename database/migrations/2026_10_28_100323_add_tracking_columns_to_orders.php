<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('orders', 'cancelled_at')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->timestamp('cancelled_at')->nullable();
            });
        }

        if (! Schema::hasColumn('orders', 'cancelled_by')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('cancelled_by', 20)->nullable();
            });
        }

        if (! Schema::hasColumn('orders', 'address_updated_at')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->timestamp('address_updated_at')->nullable();
            });
        }

        if (! Schema::hasColumn('orders', 'address_seen_at')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->timestamp('address_seen_at')->nullable();
            });
        }

        if (! Schema::hasColumn('orders', 'previous_address')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->text('previous_address')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [];

            foreach ([
                'cancelled_at',
                'cancelled_by',
                'address_updated_at',
                'address_seen_at',
                'previous_address',
            ] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $columns[] = $column;
                }
            }

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};

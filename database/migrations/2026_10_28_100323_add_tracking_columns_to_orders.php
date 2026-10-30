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
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [];

            foreach ([
                'cancelled_at',
                'cancelled_by',
                'address_updated_at',
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

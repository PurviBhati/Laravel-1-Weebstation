
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add the column only if it doesn't already exist.
        if (! Schema::hasColumn('category', 'audience_id')) {
            Schema::table('category', function (Blueprint $table) {
                $table->unsignedBigInteger('audience_id')->nullable();
            });
        }

        // Add the foreign key only if it isn't already present.
        $foreignKeyExists = DB::selectOne("
            SELECT COUNT(*) AS total
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'category'
              AND COLUMN_NAME = 'audience_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ")->total > 0;

        if (! $foreignKeyExists) {
            Schema::table('category', function (Blueprint $table) {
                $table->foreign('audience_id')
                    ->references('id')
                    ->on('audiences')
                    ->nullOnDelete();
            });
        }

        // Match existing category audience values.
        DB::statement("
            UPDATE category
            JOIN audiences ON audiences.slug =
                CASE
                    WHEN category.for_whom = 'everyone' THEN 'unisex'
                    ELSE category.for_whom
                END
            SET category.audience_id = audiences.id
            WHERE category.audience_id IS NULL
        ");
    }

    public function down(): void
    {
        // Intentionally leave the existing column and data untouched.
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // 1️⃣ Add a temporary integer column
            $table->tinyInteger('status_int')->default(1)->after('status');

        });

        // 2️⃣ Copy ENUM values to integers
        DB::table('orders')->update([
            'status_int' => DB::raw("
                CASE status
                    WHEN 'pending' THEN 1
                    WHEN 'paid' THEN 2
                    WHEN 'shipped' THEN 3
                    WHEN 'completed' THEN 4
                    WHEN 'cancelled' THEN 5
                END
            ")
        ]);

        Schema::table('orders', function (Blueprint $table) {

            // 3️⃣ Drop old ENUM column
            $table->dropColumn('status');

            // 4️⃣ Rename new integer column to 'status'
            $table->renameColumn('status_int', 'status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // Reverse: add ENUM again
            $table->enum('status', ['pending','paid','shipped','completed','cancelled'])
                ->default('pending')
                ->after('total_price');

            // Optional: map integers back to ENUM strings
            DB::table('orders')->update([
                'status' => DB::raw("
                    CASE status
                        WHEN 1 THEN 'pending'
                        WHEN 2 THEN 'paid'
                        WHEN 3 THEN 'shipped'
                        WHEN 4 THEN 'completed'
                        WHEN 5 THEN 'cancelled'
                    END
                ")
            ]);

            $table->dropColumn('status'); // drop integer if exists
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('orders', 'order_code')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('order_code')->nullable()->unique()->after('id')->index();
            });

            // Populate existing records with unique order_code
            $orders = DB::table('orders')->get();
            foreach ($orders as $order) {
                if (empty($order->order_code) || str_starts_with($order->order_code, 'ORD-')) {
                    do {
                        $code = strtoupper(Str::random(8));
                    } while (DB::table('orders')->where('order_code', $code)->exists());

                    DB::table('orders')->where('id', $order->id)->update([
                        'order_code' => $code,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('orders', 'order_code')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('order_code');
            });
        }
    }
};

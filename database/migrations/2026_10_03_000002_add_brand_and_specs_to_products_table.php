<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('id')->constrained('brands')->nullOnDelete();
            $table->text('uses')->nullable()->after('description')->comment('Công dụng sản phẩm');
            $table->text('ingredients')->nullable()->after('uses')->comment('Thành phần / chất liệu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn(['brand_id', 'uses', 'ingredients']);
        });
    }
};

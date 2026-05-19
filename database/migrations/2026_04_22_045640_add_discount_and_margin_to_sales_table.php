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
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('id_diskon')->nullable()->after('total_bayar');
            $table->decimal('total_diskon', 15, 2)->default(0)->after('id_diskon');
            
            $table->foreign('id_diskon')->references('id')->on('discounts')->onDelete('set null');
        });

        Schema::table('sale_details', function (Blueprint $table) {
            $table->decimal('diskon', 15, 2)->default(0)->after('jumlah_jual');
            $table->decimal('margin', 15, 2)->default(0)->after('diskon');
        });
        
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('id_diskon')->nullable()->after('total_bayar');
            $table->decimal('total_diskon', 15, 2)->default(0)->after('id_diskon');
            
            $table->foreign('id_diskon')->references('id')->on('discounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['id_diskon']);
            $table->dropColumn(['id_diskon', 'total_diskon']);
        });

        Schema::table('sale_details', function (Blueprint $table) {
            $table->dropColumn(['diskon', 'margin']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['id_diskon']);
            $table->dropColumn(['id_diskon', 'total_diskon']);
        });
    }
};

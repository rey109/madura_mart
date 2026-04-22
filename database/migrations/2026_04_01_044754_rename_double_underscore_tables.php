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
        Schema::rename('purchase__details', 'purchase_details');
        Schema::rename('sale__details', 'sale_details');
        Schema::rename('order__details', 'order_details');

        // Update the vwPurchase view to use the new table name
        \Illuminate\Support\Facades\DB::statement("
            CREATE OR REPLACE VIEW vwPurchase AS
            SELECT 
                P.id,
                P.no_nota,
                P.tgl_nota,
                P.id_distributor,
                D.nama_distributor,
                PD.id AS id_PD,
                PD.id_barang,
                PR.nama_barang,
                PD.harga_beli,
                PD.margin_jual,
                PD.jumlah_beli,
                PD.subtotal,
                P.total_bayar
            FROM purchases P
            JOIN purchase_details PD ON P.id = PD.id_pembelian
            JOIN distributors D ON P.id_distributor = D.id
            JOIN products PR ON PD.id_barang = PR.id;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('purchase_details', 'purchase__details');
        Schema::rename('sale_details', 'sale__details');
        Schema::rename('order_details', 'order__details');

        // Restore the old view SQL
        \Illuminate\Support\Facades\DB::statement("
            CREATE OR REPLACE VIEW vwPurchase AS
            SELECT 
                P.id,
                P.no_nota,
                P.tgl_nota,
                P.id_distributor,
                D.nama_distributor,
                PD.id AS id_PD,
                PD.id_barang,
                PD.harga_beli,
                PD.margin_jual,
                PD.jumlah_beli,
                PD.subtotal,
                P.total_bayar
            FROM purchases P
            JOIN purchase__details PD ON P.id = PD.id_pembelian
            JOIN distributors D ON P.id_distributor = D.id;
        ");
    }

};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE VIEW vwPurchase AS
SELECT 
    P.id,
    P.no_nota,
    P.tgl_nota,
    P.id_distributor,
    D.nama_distributor,
    PD.id AS id_PD,
    PD.id_barang,
    B.nama_barang,
    B.jenis_barang,
    B.tgl_expired,
    B.harga_jual,
    B.stok,
    B.foto_barang,
    PD.harga_beli,
    PD.margin_jual,
    PD.jumlah_beli,
    PD.subtotal,
    P.total_bayar
FROM purchases P
JOIN purchase__details PD ON P.id = PD.id_pembelian
JOIN distributors D ON P.id_distributor = D.id
JOIN products B ON PD.id_barang = B.id;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vwPurchase");
    }
};


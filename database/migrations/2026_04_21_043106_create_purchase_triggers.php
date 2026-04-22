<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates 3 MySQL Triggers on the `purchase_details` table:
     * 
     * 1. trg_purchase_detail_before_insert
     *    - Auto-calculates subtotal = harga_beli * jumlah_beli
     * 
     * 2. trg_purchase_detail_after_insert
     *    - Increments product stock (stok) in `products` table
     *    - Recalculates total_bayar in `purchases` table
     * 
     * 3. trg_purchase_detail_after_delete
     *    - Decrements product stock (stok) in `products` table
     *    - Recalculates total_bayar in `purchases` table
     */
    public function up(): void
    {
        // ─── Trigger 1: BEFORE INSERT ─────────────────────────────────
        // Auto-calculate subtotal before the row is inserted
        DB::unprepared("
            CREATE TRIGGER trg_purchase_detail_before_insert
            BEFORE INSERT ON purchase_details
            FOR EACH ROW
            BEGIN
                SET NEW.subtotal = NEW.harga_beli * NEW.jumlah_beli;
            END
        ");

        // ─── Trigger 2: AFTER INSERT ──────────────────────────────────
        // Update product stock (+) and recalculate purchase total
        DB::unprepared("
            CREATE TRIGGER trg_purchase_detail_after_insert
            AFTER INSERT ON purchase_details
            FOR EACH ROW
            BEGIN
                -- Increment product stock
                UPDATE products 
                SET stok = stok + NEW.jumlah_beli 
                WHERE id = NEW.id_barang;

                -- Recalculate purchase total_bayar
                UPDATE purchases 
                SET total_bayar = (
                    SELECT COALESCE(SUM(subtotal), 0) 
                    FROM purchase_details 
                    WHERE id_pembelian = NEW.id_pembelian
                )
                WHERE id = NEW.id_pembelian;
            END
        ");

        // ─── Trigger 3: AFTER DELETE ──────────────────────────────────
        // Revert product stock (-) and recalculate purchase total
        DB::unprepared("
            CREATE TRIGGER trg_purchase_detail_after_delete
            AFTER DELETE ON purchase_details
            FOR EACH ROW
            BEGIN
                -- Decrement product stock
                UPDATE products 
                SET stok = stok - OLD.jumlah_beli 
                WHERE id = OLD.id_barang;

                -- Recalculate purchase total_bayar
                UPDATE purchases 
                SET total_bayar = (
                    SELECT COALESCE(SUM(subtotal), 0) 
                    FROM purchase_details 
                    WHERE id_pembelian = OLD.id_pembelian
                )
                WHERE id = OLD.id_pembelian;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_purchase_detail_before_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_purchase_detail_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_purchase_detail_after_delete');
    }
};

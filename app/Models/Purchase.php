<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\HasAutoNumber;

/**
 * Class Purchase
 * 
 * Represents a procurement transaction from a distributor.
 * 
 * @package App\Models
 */
class Purchase extends Model
{
    use HasAutoNumber;

    protected $fillable = ['no_nota', 'tgl_nota', 'id_distributor', 'total_bayar'];

    public function getAutoNumberField(): string
    {
        return 'no_nota';
    }

    public function getAutoNumberPrefix(): string
    {
        return 'PURCH';
    }

    /**
     * Store a complete purchase set (Header + Details) and update stock.
     * Encapsulates the entire business transaction.
     * 
     * @param array $data Validated request data
     * @return Purchase
     * @throws \Exception
     */
    public static function storeAsSet(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Create purchase header with total_bayar = 0
            // (Trigger will recalculate total_bayar after details are inserted)
            $purchase = self::create([
                'no_nota' => $data['no_nota'] ?? null,
                'tgl_nota' => $data['tgl_nota'],
                'id_distributor' => $data['id_distributor'],
                'total_bayar' => 0
            ]);

            foreach ($data['products'] as $index => $productId) {
                $qty = $data['quantities'][$index];
                $price = $data['buy_prices'][$index];
                $margin = $data['margins'][$index] ?? 0;

                // Insert detail — triggers will handle:
                // 1. BEFORE INSERT: auto-calculate subtotal
                // 2. AFTER INSERT: increment stok + recalculate total_bayar
                $purchase->details()->create([
                    'id_barang' => $productId,
                    'harga_beli' => $price,
                    'margin_jual' => $margin,
                    'jumlah_beli' => $qty,
                    'subtotal' => 0  // Will be overridden by BEFORE INSERT trigger
                ]);
            }

            // Refresh to get the trigger-updated total_bayar
            $purchase->refresh();

            return $purchase;
        });
    }

    /**
     * Get the distributor that supplied this purchase.
     */
    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'id_distributor');
    }

    /**
     * Get the itemized details for this purchase.
     */
    public function details()
    {
        return $this->hasMany(PurchaseDetail::class, 'id_pembelian');
    }
}



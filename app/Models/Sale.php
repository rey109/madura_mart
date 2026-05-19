<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\HasAutoNumber;
use App\Models\Product;
use App\Models\PurchaseDetail;
use App\Models\SaleDetail;

/**
 * Class Sale
 * 
 * Represents a direct customer sale.
 * 
 * @package App\Models
 */
class Sale extends Model
{
    use HasAutoNumber;

    protected $fillable = ['no_struk', 'tgl_jual', 'total_bayar'];

    public function getAutoNumberField(): string
    {
        return 'no_struk';
    }

    public function getAutoNumberPrefix(): string
    {
        return 'SALE';
    }

    /**
     * Store a complete sale set (Header + Details) and update stock.
     * Encapsulates the entire business transaction.
     * 
     * @param array $data Validated request data
     * @return Sale
     * @throws \Exception
     */
    public static function storeAsSet(array $data)
    {
        return DB::transaction(function () use ($data) {
            $total_bayar = 0;
            $details = [];

            foreach ($data['products'] as $index => $productId) {
                $qty = $data['quantities'][$index];
                
                $product = Product::lockForUpdate()->findOrFail($productId);
                
                // Availability Check
                if ($product->stok < $qty) {
                    throw new \Exception("Stok tidak cukup untuk produk: " . $product->nama_barang . " (Sisa: " . $product->stok . ")");
                }

                $subtotal = $product->harga_jual * $qty;
                $total_bayar += $subtotal;

                // Decrease Stock
                $product->decrement('stok', $qty);

                // Calculate Margin (Profit)
                $latestPurchase = PurchaseDetail::where('id_barang', $productId)->latest()->first();
                $buyPrice = $latestPurchase ? $latestPurchase->harga_beli : ($product->harga_jual * 0.8); // Fallback to 20% margin if no purchase history
                $itemMargin = $product->harga_jual - $buyPrice;

                $details[] = [
                    'id_barang' => $productId,
                    'harga_jual' => $product->harga_jual,
                    'jumlah_jual' => $qty,
                    'subtotal' => $subtotal,
                    'diskon' => 0, // Currently transaction-level, can be expanded to item-level
                    'margin' => $itemMargin
                ];
            }

            // Apply Transaction Discount
            $finalTotal = $total_bayar - ($data['total_diskon'] ?? 0);

            $sale = self::create([
                'no_struk' => $data['no_struk'] ?? null,
                'tgl_jual' => $data['tgl_jual'],
                'total_bayar' => $finalTotal,
                'id_diskon' => $data['id_diskon'] ?? null,
                'total_diskon' => $data['total_diskon'] ?? 0
            ]);

            foreach ($details as $detail) {
                $sale->details()->create($detail);
            }

            return $sale;
        });
    }

    /**
     * Get the itemized details of this sale.
     */
    public function details()
    {
        return $this->hasMany(SaleDetail::class, 'id_penjualan');
    }
}




<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Sale
 * 
 * Represents a direct customer sale.
 * 
 * @package App\Models
 */
class Sale extends Model
{
    protected $fillable = ['no_struk', 'tgl_jual', 'total_bayar'];

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

                $details[] = [
                    'id_barang' => $productId,
                    'harga_jual' => $product->harga_jual,
                    'jumlah_jual' => $qty,
                    'subtotal' => $subtotal
                ];
            }

            $sale = self::create([
                'no_struk' => $data['no_struk'],
                'tgl_jual' => $data['tgl_jual'],
                'total_bayar' => $total_bayar
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




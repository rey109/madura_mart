<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Order
 * 
 * Represents a customer order.
 * 
 * @package App\Models
 */
class Order extends Model
{
    protected $fillable = ['tgl_pemesanan', 'id_pelanggan', 'status_pemesanan', 'metode_pembayaran', 'total_bayar', 'keterangan_status'];

    /**
     * Store a complete order set (Header + Details) and reserve stock.
     * 
     * @param array $data Validated request data
     * @return Order
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
                
                if ($product->stok < $qty) {
                    throw new \Exception("Stok tidak cukup untuk produk: " . $product->nama_barang . " (Sisa: " . $product->stok . ")");
                }

                $subtotal = $product->harga_jual * $qty;
                $total_bayar += $subtotal;

                // Reserve Stock
                $product->decrement('stok', $qty);

                $details[] = [
                    'id_barang' => $productId,
                    'harga_jual' => $product->harga_jual,
                    'jumlah_jual' => $qty,
                    'subtotal' => $subtotal,
                    'catatan' => $data['item_notes'][$index] ?? null
                ];
            }

            $order = self::create([
                'tgl_pemesanan' => $data['tgl_pemesanan'],
                'id_pelanggan' => $data['id_pelanggan'],
                'status_pemesanan' => $data['status_pemesanan'],
                'metode_pembayaran' => $data['metode_pembayaran'],
                'total_bayar' => $total_bayar,
                'keterangan_status' => $data['keterangan_status'] ?? null
            ]);

            foreach ($details as $detail) {
                $order->details()->create($detail);
            }

            return $order;
        });
    }

    /**
     * Get the client who placed this order.
     */
    public function pelanggan()
    {
        return $this->belongsTo(Client::class, 'id_pelanggan');
    }

    /**
     * Get the itemized details for this order.
     */
    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'id_pemesanan');
    }

    /**
     * Get the delivery information for this order.
     */
    public function delivery()
    {
        return $this->hasOne(Delivery::class, 'id_pemesanan');
    }
}



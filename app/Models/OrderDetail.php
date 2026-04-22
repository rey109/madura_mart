<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderDetail
 * 
 * Represents an itemized entry in a customer order.
 * 
 * @package App\Models
 */
class OrderDetail extends Model
{
    protected $fillable = ['id_pemesanan', 'id_barang', 'harga_jual', 'jumlah_jual', 'subtotal', 'catatan'];

    /**
     * Get the order header this detail belongs to.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'id_pemesanan');
    }

    /**
     * Get the product sold in this order item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_barang');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SaleDetail
 * 
 * Represents an itemized entry in a customer sale.
 * 
 * @package App\Models
 */
class SaleDetail extends Model
{
    protected $fillable = ['id_penjualan', 'id_barang', 'harga_jual', 'jumlah_jual', 'subtotal'];

    /**
     * Get the sale header this detail belongs to.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'id_penjualan');
    }

    /**
     * Get the product sold in this item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_barang');
    }
}


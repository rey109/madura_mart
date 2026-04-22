<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PurchaseDetail
 * 
 * Represents an itemized entry in a Purchase.
 * Joins a Purchase header with a specific Product.
 * 
 * @package App\Models
 * @property int $id
 * @property int $id_pembelian
 * @property int $id_barang
 * @property int $harga_beli
 * @property int $margin_jual
 * @property int $jumlah_beli
 * @property int $subtotal
 */
class PurchaseDetail extends Model
{
    protected $fillable = ['id_pembelian', 'id_barang', 'harga_beli', 'margin_jual', 'jumlah_beli', 'subtotal'];

    /**
     * Get the purchase header this detail belongs to.
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'id_pembelian');
    }

    /**
     * Get the product associated with this item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_barang');
    }
}


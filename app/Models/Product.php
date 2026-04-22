<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Product
 * 
 * Represents an item in the inventory.
 * Supports Soft Deletes.
 */
class Product extends Model
{
    use SoftDeletes;
    protected $fillable = ['kd_barang', 'nama_barang', 'jenis_barang', 'tgl_expired', 'harga_jual', 'stok', 'foto_barang'];

    /**
     * Get the purchase items for this product.
     */
    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class, 'id_barang');
    }

    /**
     * Get the sale items for this product.
     */
    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class, 'id_barang');
    }
}


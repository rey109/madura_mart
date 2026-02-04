<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase_Detail extends Model
{
    protected $fillable = ['id_pembelian', 'id_barang', 'harga_beli', 'margin_jual', 'jumlah_beli', 'subtotal'];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'id_pembelian');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'id_barang');
    }
}

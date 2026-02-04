<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale_Detail extends Model
{
    protected $fillable = ['id_penjualan', 'id_barang', 'harga_jual', 'jumlah_jual', 'subtotal'];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'id_penjualan');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'id_barang');
    }
}

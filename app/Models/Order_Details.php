<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order_Details extends Model
{
    protected $fillable = ['id_pemesanan', 'id_barang', 'harga_jual', 'jumlah_jual', 'subtotal', 'catatan'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_pemesanan');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'id_barang');
    }
}

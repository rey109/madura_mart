<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Client;

class Order extends Model
{
    protected $fillable = ['tgl_pemesanan', 'id_pelanggan', 'status_pemesanan', 'metode_pembayaran', 'total_bayar', 'keterangan_status'];

    public function pelanggan()
    {
        return $this->belongsTo(Client::class, 'id_pelanggan');
    }

    public function details()
    {
        return $this->hasMany(Order_Details::class, 'id_pemesanan');
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class, 'id_pemesanan');
    }
}

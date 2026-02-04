<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = ['tgl_kirim', 'id_kurir', 'id_pemesanan', 'bukti_foto', 'no_invoice'];

    public function kurir()
    {
        return $this->belongsTo(Courier::class, 'id_kurir');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_pemesanan');
    }
}

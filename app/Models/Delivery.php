<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Delivery
 * 
 * Tracks the distribution/shipping of an Order.
 * 
 * @package App\Models
 * @property int $id
 * @property string $tgl_kirim
 * @property int $id_kurir
 * @property int $id_pemesanan
 * @property string $bukti_foto
 * @property string $no_invoice
 */
class Delivery extends Model
{
    protected $fillable = ['tgl_kirim', 'id_kurir', 'id_pemesanan', 'bukti_foto', 'no_invoice'];

    /**
     * Get the courier handling this delivery.
     */
    public function kurir()
    {
        return $this->belongsTo(Courier::class, 'id_kurir');
    }

    /**
     * Get the order being delivered.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'id_pemesanan');
    }
}


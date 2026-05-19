<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
        'nama_diskon', 'tipe_diskon', 'nilai_diskon', 'min_transaksi', 
        'max_diskon', 'id_barang', 'min_qty', 'tgl_mulai', 'tgl_selesai', 'status'
    ];

    public function scopeActive($query)
    {
        $now = now();
        return $query->where('status', true)
                     ->where(function($q) use ($now) {
                         $q->whereNull('tgl_mulai')->orWhere('tgl_mulai', '<=', $now);
                     })
                     ->where(function($q) use ($now) {
                         $q->whereNull('tgl_selesai')->orWhere('tgl_selesai', '>=', $now);
                     });
    }
}

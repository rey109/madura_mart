<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    //
    protected $fillable = [
        'nama_distributor',
        'alamat_distributor',
        'notelepon_distributor',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'id_distributor');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $fillable = ['nama_kurir', 'notelepon_kurir', 'plat_kendaraan'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['nama_pelanggan', 'alamat_pelanggan', 'notelepon_pelanggan'];
}

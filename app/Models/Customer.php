<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Customer
 * 
 * Represents an external client or customer.
 * 
 * @package App\Models
 * @property int $id
 * @property string $nama_pelanggan
 * @property string $alamat_pelanggan
 * @property string $notelepon_pelanggan
 */
class Customer extends Model
{
    protected $fillable = ['nama_pelanggan', 'alamat_pelanggan', 'notelepon_pelanggan'];
}



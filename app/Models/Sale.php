<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['no_struk', 'tgl_jual', 'total_bayar'];
}

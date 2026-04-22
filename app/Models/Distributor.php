<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Distributor
 * 
 * Represents a product supplier.
 * 
 * @package App\Models
 * @property int $id
 * @property string $nama_distributor
 * @property string $alamat_distributor
 * @property string $notelepon_distributor
 */
class Distributor extends Model
{
    protected $fillable = [
        'nama_distributor',
        'alamat_distributor',
        'notelepon_distributor',
    ];

    /**
     * Get the purchases associated with this distributor.
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'id_distributor');
    }
}


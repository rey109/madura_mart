<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Courier
 * 
 * Represents a delivery personnel or vehicle.
 * 
 * @package App\Models
 * @property int $id
 * @property string $nama_kurir
 * @property string $notelepon_kurir
 * @property string $plat_kendaraan
 */
class Courier extends Model
{
    protected $fillable = ['nama_kurir', 'notelepon_kurir', 'plat_kendaraan'];

    /**
     * Get the deliveries assigned to this courier.
     */
    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'id_kurir');
    }
}


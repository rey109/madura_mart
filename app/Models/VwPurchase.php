<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VwPurchase
 * 
 * Represents the vwPurchase database view.
 * This view flattens the connection between Purchases, Purchase Details, and Distributors.
 * 
 * @package App\Models
 * 
 * @property int $id
 * @property string $no_nota
 * @property string $tgl_nota
 * @property int $id_distributor
 * @property string $nama_distributor
 * @property int $id_PD
 * @property int $id_barang
 * @property int $harga_beli
 * @property int $margin_jual
 * @property int $jumlah_beli
 * @property int $subtotal
 * @property int $total_bayar
 */
class VwPurchase extends Model
{
    /**
     * The table associated with the model.
     * In this case, it is a database view.
     *
     * @var string
     */
    protected $table = 'vwPurchase';

    /**
     * Indicates if the model should be timestamped.
     * Views typically don't have timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key associated with the table.
     * Since this is a view, there is no single primary key, but we can set it to the detail ID.
     *
     * @var string
     */
    protected $primaryKey = 'id_PD';
}

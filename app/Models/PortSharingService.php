<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property int    $max_speed
 * @property int    $min_ports
 * @property string $type
 * @property int    $fees
 * @property string $currency
 * @property string $short_code
 * @property int    $status
 * @property int    $created_at
 * @property int    $updated_at
 */
class PortSharingService extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'port_sharing_services';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'max_speed',
        'min_ports',
        'type',
        'fees',
        'currency',
        'short_code',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'max_speed' => 'integer',
        'min_ports' => 'integer',
        'fees' => 'integer',
        'status' => 'integer'
    ];
}
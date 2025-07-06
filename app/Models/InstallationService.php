<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property string $type
 * @property int    $sla_hours
 * @property int    $fees
 * @property string $short_code
 * @property int    $status
 * @property int    $created_at
 * @property int    $updated_at
 */
class InstallationService extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'installation_services';

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
        'type',
        'service_type',
        'sla_hours',
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
        'sla_hours' => 'integer',
        'fees' => 'integer',
        'status' => 'integer'
    ];
}
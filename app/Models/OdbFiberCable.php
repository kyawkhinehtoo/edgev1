<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OdbFiberCable extends Model
{
    protected $table = 'odb_fiber_cables';

    protected $fillable = [
        'odb_id',
        'fiber_cable_id',
        'pop_device_id', // nullable
        'odb_port',
        'olt_port', // nullable
        'olt_cable_label',
        'description',
        'status'
    ];

    protected $casts = [
        'odb_port' => 'integer',
        'olt_port' => 'integer',
        'status' => 'string'
    ];

    /**
     * Get the ODF that owns this connection.
     */
    public function odb()
    {
        return $this->belongsTo(Odb::class);
    }

    /**
     * Get the fiber cable that owns this connection.
     */
    public function fiberCable()
    {
        return $this->belongsTo(FiberCable::class);
    }
    public function popDevice()
    {
        return $this->belongsTo(PopDevice::class);
    }
}
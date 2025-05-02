<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Odf extends Model
{
    protected $fillable = [
        'pop_device_id',
        'name',
        'location',
        'description'
    ];

    protected $casts = [
        'pop_device_id' => 'array'
    ];

    public function popDevice()
    {
        return $this->belongsToMany(PopDevice::class, null, 'pop_device_id');
    }

    /**
     * Get the fiber cable connections for this ODF.
     */
    public function fiberCables()
    {
        return $this->hasMany(OdfFiberCable::class);
    }
}
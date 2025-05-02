<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnSplitter extends Model
{
    protected $fillable = [
        'name',
        'sn_id',
        'fiber_id',
        'core_number',
        'port_number',
        'location',
        'status',
        'fiber_type',
    ];
    public function snBox()
    {
        return $this->belongsTo(SnBox::class, 'sn_id');
    }

    public function fiberCable()
    {
        return $this->belongsTo(FiberCable::class, 'fiber_id');
    }
}

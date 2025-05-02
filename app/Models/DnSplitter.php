<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DnSplitter extends Model
{
    protected $fillable = [
        'name',
        'dn_id',
        'fiber_id',
        'core_number',
        'location',
        'status',
    ];
    public function dnBox()
    {
        return $this->belongsTo(DnBox::class, 'dn_id');
    }
    public function fiberCable()
    {
        return $this->belongsTo(FiberCable::class, 'fiber_id');
    }
}

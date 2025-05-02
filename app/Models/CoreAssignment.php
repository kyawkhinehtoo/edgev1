<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoreAssignment extends Model
{
    protected $fillable = [
        'source_id',
        'source_color',
        'source_port',
        'dest_id',
        'dest_color',
        'dest_port',
        'jc_id',
        'status',
    ];

    /**
     * Get the source fiber cable associated with the core assignment.
     */
    public function sourceFiberCable()
    {
        return $this->belongsTo(FiberCable::class, 'source_id');
    }

    /**
     * Get the destination fiber cable associated with the core assignment.
     */
    public function destinationFiberCable()
    {
        return $this->belongsTo(FiberCable::class, 'dest_id');
    }

    /**
     * Get the JC Box associated with the core assignment.
     */
    public function jcBox()
    {
        return $this->belongsTo(JcBox::class, 'jc_id');
    }
}
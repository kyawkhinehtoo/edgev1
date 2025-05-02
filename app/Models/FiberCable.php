<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FiberCable extends Model
{
    protected $fillable = [
        'name',
        'core_quantity',
        'type',
        'cable_layout',
        'cable_length',
        'status'
    ];

    /**
     * Get the core assignments where this cable is the source.
     */
    public function sourceAssignments()
    {
        return $this->hasMany(CoreAssignment::class, 'source_id');
    }

    /**
     * Get the core assignments where this cable is the destination.
     */
    public function destinationAssignments()
    {
        return $this->hasMany(CoreAssignment::class, 'dest_id');
    }
    public function dnSplitter()
    {
        return $this->hasMany(DnSplitter::class, 'fiber_id');
    }
   
}

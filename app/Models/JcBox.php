<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JcBox extends Model
{
    protected $fillable = [
        'name',
        'location',
        'type',
        'status'
    ];

    /**
     * Get the core assignments associated with this JC Box.
     */
    public function coreAssignments()
    {
        return $this->hasMany(CoreAssignment::class, 'jc_id');
    }
}

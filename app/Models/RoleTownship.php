<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleTownship extends Model
{
    protected $table = 'role_township';

    protected $fillable = [
        'role_id',
        'township_id'
    ];

    /**
     * Get the role that owns the township.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the township that belongs to the role.
     */
    public function township()
    {
        return $this->belongsTo(Township::class);
    }
}
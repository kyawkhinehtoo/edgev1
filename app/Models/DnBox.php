<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DnBox extends Model
{
    protected $fillable = [
        'name',
        'location',
        'description',
        'status',
    ];

    public function dnSplitters()
    {
        return $this->hasMany(DnSplitter::class, 'dn_id');
    }
}

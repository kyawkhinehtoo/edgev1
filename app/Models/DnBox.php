<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DnBox extends Model
{
    protected $fillable = [
        'name',
        'location',
        'type',
        'township_id',
        'description',
        'status',
    ];

    public function dnSplitters()
    {
        return $this->hasMany(DnSplitter::class, 'dn_id');
    }
    public function township()
    {
        return $this->belongsTo(Township::class);
    }
   
}

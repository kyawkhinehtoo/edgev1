<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnBox extends Model
{
    protected $fillable = [
        'name',
        'dn_splitter_id',
        'location',
        'status',
    ];
    public function dnSplitter()
    {
        return $this->belongsTo(DnSplitter::class, 'dn_splitter_id');
    }
}

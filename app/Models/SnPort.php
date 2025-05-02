<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnPort extends Model
{
    protected $fillable = [
        'name',
        'sn_splitter_id',
        'port_number',
        'customer_id',
        'status'
    ];
    public function snSplitter()
    {
        return $this->belongsTo(SnSplitter::class, 'sn_splitter_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnPort extends Model
{
    protected $fillable = [
        'name',
        'sn_splitter_id',
        'dn_splitter_id',
        'pop_device_id',
        'pop_id',
        'dn_box_id',
        'port_number',
        'customer_id',
        'status'
    ];
    public function snSplitter()
    {
        return $this->belongsTo(SnSplitter::class, 'sn_splitter_id');
    }
    public function dnSplitter()
    {
        return $this->belongsTo(DnSplitter::class, 'dn_splitter_id');
    }
    public function popDevice()
    {
        return $this->belongsTo(PopDevice::class, 'pop_device_id');
    }
    public function pop()  
    {
        return $this->belongsTo(Pop::class, 'pop_id');
    }
    public function dnBox()
    {
        return $this->belongsTo(DnBox::class, 'dn_box_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}

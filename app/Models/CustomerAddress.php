<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    protected $table = 'customer_addresses';

    protected $fillable = [
        'customer_id',
        'township_id',
        'location',
        'actual_location',
        'address',
        'is_current',
        'type',
        'installation_date',
        'relocation_service_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function township()
    {
        return $this->belongsTo(Township::class);
    }
}

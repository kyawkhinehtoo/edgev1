<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempBill extends Model
{
    protected $table = 'temp_bills';

    protected $fillable = [
        'name',
        'bill_number',
        'billing_period',
        'status',
        'exchange_rate',
        'created_at',
        'updated_at'
    ];

    public function tempInvoices()
    {
        return $this->hasMany(TempInvoice::class);
    }
}
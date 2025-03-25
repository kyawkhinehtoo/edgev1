<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempInvoiceItem extends Model
{
    protected $table = 'temp_invoice_items';

    protected $fillable = [
        'temp_invoice_id',
        'customer_id',
        'type',
        'start_date',
        'end_date',
        'quantity',
        'unit_price',
        'total_amount',
        'description',
        'created_at',
        'updated_at'
    ];

    public function tempInvoice()
    {
        return $this->belongsTo(TempInvoice::class, 'temp_invoice_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
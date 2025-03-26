<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $table = 'invoice_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'invoice_id',
        'customer_id',
        'type',
        'start_date',
        'end_date',
        'quantity',
        'unit_price',
        'total_amount',
        'description'
    ];

    // protected $casts = [
    //     'start_date' => 'datetime',
    //     'end_date' => 'datetime'
    // ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
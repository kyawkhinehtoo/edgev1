<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempInvoice extends Model
{
    protected $table = 'temp_invoices';

    protected $fillable = [
        'temp_bill_id',
        'isp_id',
        'issue_date',
        'due_date',
        'total_mrc_amount',
        'total_installation_amount',
        'total_mrc_customer',
        'total_new_customer',
        'additional_description',
        'additional_fees',
        'discount_amount',
        'sub_total',
        'tax_amount',
        'tax_percent',
        'total_amount',
        'created_at',
        'updated_at'
    ];

    public function tempBill()
    {
        return $this->belongsTo(TempBill::class, 'temp_bill_id');
    }

    public function isp()
    {
        return $this->belongsTo(Isp::class, 'isp_id');
    }

    public function tempInvoiceItems()
    {
        return $this->hasMany(TempInvoiceItem::class, 'temp_invoice_id');
    }
}
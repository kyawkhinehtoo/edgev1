<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bill_id',
        'receipt_id',
        'invoice_number',
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
        'payment_status',
        'sms_status',
        'email_status',
        'sms_date',
        'email_date',
        'invoice_file',
        'invoice_url',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'sms_date' => 'datetime',
        'email_date' => 'datetime',
        
    ];

    public static function generateInvoiceNumber($bill_id)
    {
        // Get the last invoice for the given billing month
        $latestInvoice = self::where('bill_id', $bill_id)
                             ->orderBy('id', 'desc')
                             ->first();
    
        // Extract last sequence number and increment
        $lastNumber = $latestInvoice ? intval(substr($latestInvoice->invoice_number, -3)) : 0;
        $newSequence = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    
        // Generate invoice number format
        return $newSequence;
    }
    
    public function bill()
    {
        return $this->belongsTo(Bills::class, 'bill_id');
    }

    public function isp()
    {
        return $this->belongsTo(Isp::class, 'isp_id');
    }

    public function invoiceItem()
    {
        return $this->hasMany(invoiceItem::class, 'invoice_id');
    }
    public function receiptRecord()
    {
        return $this->belongsTo(ReceiptRecord::class, 'receipt_id');
    }
}

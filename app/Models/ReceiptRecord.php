<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTime;

class ReceiptRecord extends Model
{

    protected $table = 'receipt_records';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'isp_id',
        'bill_id',
        'receipt_number',
        'status',
        'issue_amount',
        'issue_currenty',
        'receipt_person',
        'payment_channel',
        'collected_person',
        'receipt_date',
        'remark',
        'collected_amount',
        'outstanding_amount',
        'collected_currency',
        'collected_exchangerate',
        'receipt_file',
        'receipt_url',
        'created_at',
        'updated_at',
        'transition'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'receipt_date' => 'datetime:Y-m-d',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'receipt_date',
        'created_at',
        'updated_at'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = false;

    // Scopes...

    // Functions ...

    // Relations ...
    public function getCreatedAtAttribute($date)
    {
        return  date('d-M-Y H:i:s', strtotime($date));
    }
   
    public function invoice()
    {
        return $this->hasMany(Invoice::class, 'receipt_id');
    }

    public function isp()
    {
        return $this->belongsTo(Isp::class, 'isp_id');
    }
    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }
    public function collectedPerson()
    {
        return $this->belongsTo(User::class, 'collected_person');
    }
    public static function generateReceiptNumber($bill_id)
    {
        // Get the last receipt for the given billing month
        $latestReceipt = self::where('bill_id', $bill_id)
                             ->orderBy('id', 'desc')
                             ->first();
    
        // Extract last sequence number and increment
        $lastNumber = $latestReceipt ? intval(substr($latestReceipt->invoice_number, -3)) : 0;
        $newSequence = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    
        // Generate receipt number format
        return $newSequence;
    }
    
}

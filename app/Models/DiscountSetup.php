<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscountSetup extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'discount_setup';

    protected $fillable = [
        'name',
        'port_sharing_service_id',
        'isp_id',
        'start_date',
        'end_date',
        'fix_rate',
        'rate_type',
        'discount_percentage',
        'description',
        'is_active',
        'created_by',
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'deleted_at',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

  

    // Relationships
    public function portSharingService()
    {
        return $this->belongsTo(PortSharingService::class);
    }

    public function isp()
    {
        return $this->belongsTo(Isp::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Isp extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'isps';

    // Add to the fillable array
    protected $fillable = [
        'name',
        'short_code',
        'contact_person',
        'phone',
        'email',
        'address',
        'website',
        'domain',
        'logo',
        'description',
        'service_type',
        'bandwidth_capacity',
        'is_active',
        'permissions',
        'customer_status',
    ];

    // Add to the casts array
    protected $casts = [
        'permissions' => 'json',
        'customer_status' => 'json',
    ];
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function customer()
    {
        return $this->hasMany(Customer::class);
    }
    public function subcoms()
    {
        return $this->belongsToMany(Subcom::class, 'subcom_isp')
                    ->withTimestamps();
    }
    public static function findByDomain($domain)
    {
        return self::where('domain', $domain)->first();
    }
    public static function checkDuplicate($domain){
        return self::where('domain', $domain)->exists();
    }
}

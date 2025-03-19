<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'website',
        'domain',
        'logo',
        'description',
        'is_active',
        'permissions',
        'customer_status',
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'permissions' => 'array',
        'customer_status' => 'array',
    ];
 

    public function users()
        {
            return $this->hasMany(User::class);
        }
    public function pops()
        {
            return $this->hasMany(Pop::class);
        }
    public function townships()
        {
        return $this->hasMany(Township::class);
        }
    public static function findByDomain($domain)
    {
        return self::where('domain', $domain)->first();
    }
    public static function checkDuplicate($domain){
        return self::where('domain', $domain)->exists();
    }
}

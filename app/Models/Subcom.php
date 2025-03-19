<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subcom extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'name', 'rate', 'contact_person', 'email', 'phone', 'disabled','type' ,'installation','maintenance','maintenance_fees','created_at', 'updated_at',    'permissions',
        'customer_status',
    ];
    protected $casts = [
        'disabled' => 'boolean',
        'permissions' => 'array',
        'customer_status' => 'array',
    ];
    public function installationTownships()
    {
        return $this->belongsToMany(Township::class, 'subcom_township_installation')
                    ->withTimestamps();
    }

    public function maintenanceTownships()
    {
        return $this->belongsToMany(Township::class, 'subcom_township_maintenance')
                    ->withTimestamps();
    }
    public function isps()
    {
        return $this->belongsToMany(Isp::class, 'subcom_isp')
                    ->withTimestamps();
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
}

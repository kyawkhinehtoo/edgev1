<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Township extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'townships';

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
        'name', 'city_id','short_code','partner_id', 'created_at', 'updated_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string', 'city_id' => 'integer', 'short_code' => 'string', 'created_at' => 'timestamp', 'updated_at' => 'timestamp'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at'
    ];

    // Scopes...

    // Functions ...

    // Relations ...
    // original
    // public function customer()
    // {
    //     return $this->belongsTo(Customer::class);
    // }
    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function zones(): BelongsToMany
    {
        return $this->belongsToMany(Zone::class, 'township_zone')
                    ->withTimestamps();
    }
    public function installationSubcoms()
    {
        return $this->belongsToMany(Subcom::class, 'subcom_township_installation')
                    ->withTimestamps();
    }

    public function maintenanceSubcoms()
    {
        return $this->belongsToMany(Subcom::class, 'subcom_township_maintenance')
                    ->withTimestamps();
    }
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}

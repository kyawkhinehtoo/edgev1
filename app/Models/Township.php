<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Township extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city_id',
        'short_code',
        'partner_id',
        'created_at',
        'updated_at'
    ];

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
    /**
     * Get the customer addresses for the township.
     */
    public function customerAddresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    /**
     * Get the zones that belong to the township.
     */
    public function zones(): BelongsToMany
    {
        return $this->belongsToMany(Zone::class);
    }

    /**
     * Get the subcoms that belong to the township.
     */
    public function subcoms(): BelongsToMany
    {
        return $this->belongsToMany(Subcom::class);
    }

    /**
     * Get the ODBs for the township.
     */
    public function dnBox(): HasMany
    {
        return $this->hasMany(DnBox::class);
    }
}

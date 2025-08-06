<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Zone extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'city_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function townships(): BelongsToMany
    {
        return $this->belongsToMany(Township::class, 'township_zone')
                    ->withTimestamps();
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
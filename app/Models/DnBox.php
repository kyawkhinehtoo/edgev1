<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DnBox extends Model
{
    protected $fillable = [
        'name',
        'location',
        'type',
        'township_id',
        'description',
        'status',
    ];

    public function dnSplitters()
    {
        return $this->hasMany(DnSplitter::class, 'dn_id');
    }
    public function township()
    {
        return $this->belongsTo(Township::class);
    }

    public function scopeNearby($query, $location, float $radius = 10)
    {
        // Assuming $location is a string in the format "lat,lng"
        [$lat, $lng] = explode(',', $location);
        // Convert radius from meters to kilometers
        $radiusKm = $radius / 1000;
        return $query
            ->with(['township'])
            ->whereRaw("location REGEXP '^[0-9\\.\-]+,[0-9\\.\-]+$'")
            ->select('*')
            ->selectRaw(
            '(6371000 * acos(
                cos(radians(?)) *
                cos(radians(SUBSTRING_INDEX(location, ",", 1))) *
                cos(radians(SUBSTRING_INDEX(location, ",", -1)) - radians(?)) +
                sin(radians(?)) *
                sin(radians(SUBSTRING_INDEX(location, ",", 1)))
            )) AS distance',
            [$lat, $lng, $lat]
            )
            ->having('distance', '<=', $radius)
            ->orderBy('distance');
    }
   
}

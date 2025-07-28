<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnSplitter extends Model
{
    protected $fillable = [
        'name',
        'sn_id',
        'fiber_id',
        'core_number',
        'port_number',
        'location',
        'status',
        'fiber_type',
    ];
    public function snBox()
    {
        return $this->belongsTo(SnBox::class, 'sn_id');
    }

    public function fiberCable()
    {
        return $this->belongsTo(FiberCable::class, 'fiber_id');
    }
    public function scopeNearby($query, $location, float $radius = 10)
    {
        // Assuming $location is a string in the format "lat,lng"
        [$lat, $lng] = explode(',', $location);
        // Convert radius from meters to kilometers
        $radiusKm = $radius / 1000;
        return $query
            ->with([
            'snBox',
            'snBox.dnSplitter',
            'snBox.dnSplitter.popDevice',
            'snBox.dnSplitter.dnBox',
            'snBox.dnSplitter.popDevice.pop',
            'snBox.dnSplitter.popDevice.pop.partner'
            ])
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

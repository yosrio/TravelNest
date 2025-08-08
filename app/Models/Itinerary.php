<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Itinerary extends Model
{
    use HasUuids;

    protected $table = 'itineraries';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'trip_id',
        'day',
        'location',
        'activity',
        'description',
        'time_of_day',
        'scheduled_time',
        'map_link',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'day' => 'integer',
        'scheduled_time' => 'datetime:H:i',
        'sort_order' => 'integer',
    ];

    /**
     * Get the trip that this itinerary belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}

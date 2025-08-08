<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Trip extends Model
{
    use HasUuids;

    protected $table = 'trips';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'type',
        'destination',
        'start_date',
        'end_date',
        'status',
        'visibility',
        'notes',
        'cover_image',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * The user that owns the Trip
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The itineraries that belong to the Trip
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function itineraries()
    {
        return $this->hasMany(Itinerary::class);
    }

    /**
     * Get the budgets that belong to the Trip
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    /**
     * Get the expenses that belong to the Trip.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function expenses()
    {
        return $this->hasManyThrough(Expense::class, Budget::class);
    }

    /**
     * The checklists that belong to the Trip
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function checklists()
    {
        return $this->hasMany(Checklist::class);
    }

    /**
     * Get the documents that belong to the Trip
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * The photos that belong to the Trip
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    /**
     * The reviews that belong to the Trip
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the shared tokens that belong to the Trip
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sharedTokens()
    {
        return $this->hasMany(SharedToken::class);
    }
}


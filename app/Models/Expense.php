<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Expense extends Model
{
    use HasUuids;

    protected $table = 'expenses';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'budget_id',
        'description',
        'amount',
        'spent_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'spent_at' => 'date',
    ];

    /**
     * Get the budget that owns the Expense
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    /**
     * Get the trip that owns the Expense
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function trip()
    {
        return $this->hasOneThrough(Trip::class, Budget::class);
    }
}

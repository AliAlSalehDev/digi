<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MealSession extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'user_id',
        'status',
        'current_day',
        'total_days',
        'goal',
        'goal_explanation',
        'meal_data',
        'daily_totals',
        'total_calories',
        'total_protein',
        'total_carbs',
        'total_fat',
        'total_price',
        'total_meals',
        'error_message',
        'started_at',
        'completed_at'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'meal_data' => 'array',
        'daily_totals' => 'array',
        'total_calories' => 'decimal:2',
        'total_protein' => 'decimal:2',
        'total_carbs' => 'decimal:2',
        'total_fat' => 'decimal:2',
        'total_price' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    /**
     * Get the user that owns the meal session.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for completed sessions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for processing sessions
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope for failed sessions
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Get progress percentage
     */
    public function getProgressAttribute()
    {
        if ($this->total_days == 0) {
            return 0;
        }

        return round(($this->current_day / $this->total_days) * 100, 2);
    }

    /**
     * Check if session is complete
     */
    public function isComplete()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if session is processing
     */
    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    /**
     * Check if session failed
     */
    public function hasFailed()
    {
        return $this->status === 'failed';
    }
}
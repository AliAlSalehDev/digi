<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'device_id',
        'user_hash',
        'age',
        'height',
        'weight',
        'gender',
        'activity_level',
        'neck_circumference',
        'waist_circumference',
        'hip_circumference',
        'bmi',
        'bmi_overview',
        'bmr',
        'tdee',
        'body_fat',
        'plan_period',
        'goal',
        'last_generation_at',
        'total_plans_generated'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_generation_at' => 'datetime',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'neck_circumference' => 'decimal:2',
        'waist_circumference' => 'decimal:2',
        'hip_circumference' => 'decimal:2',
        'bmi' => 'decimal:2',
        'body_fat' => 'decimal:2'
    ];

    /**
     * Get the meal sessions for the user.
     */
    public function mealSessions()
    {
        return $this->hasMany(MealSession::class);
    }

    /**
     * Get the latest meal session
     */
    public function latestMealSession()
    {
        return $this->hasOne(MealSession::class)->latest();
    }

    /**
     * Get completed meal sessions
     */
    public function completedMealSessions()
    {
        return $this->hasMany(MealSession::class)->where('status', 'completed');
    }
}

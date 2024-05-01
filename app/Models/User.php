<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\User
 *
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property string      $google_id
 * @property Carbon|null $email_verified_at
 * @property string      $password
 * @property string      $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, softDeletes;

    protected $guarded = [];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    public function groups(): belongsToMany
    {
        return $this->belongsToMany(Group::class);
    }

    public function hostingAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function appointments(): belongsToMany
    {
        return $this->belongsToMany(Appointment::class);
    }

    public function slotRules(): belongsToMany
    {
        return $this->belongsToMany(SlotRule::class);
    }

    public function breakRules(): HasMany
    {
        return $this->hasMany(BreakRule::class);
    }
}

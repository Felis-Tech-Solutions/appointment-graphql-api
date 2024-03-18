<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\AppointmentStatus
 *
 * @property int    $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 *
 * */
class AppointmentStatus extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    protected $table = 'appointment_statuses';

    //    $table = 'appointment_statuses';

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'status_id');
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class SlotRule
 *
 * @property int         $id
 * @property string|null $rule_name
 * @property string|null $day_of_the_week
 * @property string|null $start_time             Stored as 'HH:mm:ss'
 * @property string|null $end_time               Stored as 'HH:mm:ss'
 * @property int|null    $slot_duration          Duration in milliseconds
 * @property int|null    $time_after_appointment Duration in milliseconds
 * @property int|null    $appointment_type_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class SlotRule extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function users(): belongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function appointmentType(): HasOne
    {
        return $this->hasOne(AppointmentType::class);
    }

}

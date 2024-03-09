<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Appointment
 *
 * @property int         $id
 * @property string|null $title
 * @property bool|null   $description
 * @property string      $start_date_time
 * @property string      $end_date_time
 * @property string      $created_at
 * @property string      $updated_at
 *
 * */
class Appointment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}

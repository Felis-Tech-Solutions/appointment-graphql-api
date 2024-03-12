<?php

namespace App\Models;

use Carbon\Carbon;
use Cknow\Money\Money;
use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class AppointmentType
 *
 * @property int         $id
 * @property string      $name
 * @property string|null $description
 * @property int|null    $duration           Duration in minutes
 * @property Money|null  $price              Price in cents
 * @property int|null    $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class AppointmentType extends Model
{
    use HasFactory, softDeletes;

    protected $guarded = [];

    protected $casts = [
        'price' => MoneyCast::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(Appointment::class);
    }

}

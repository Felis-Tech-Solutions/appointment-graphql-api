<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BreakRule extends Model
{
    /**
     * Class BreakRule
     *
     * @property int         $id
     * @property string      $rule_name
     * @property string      $day_of_the_week
     * @property string      $start_time           Stored as 'HH:mm:ss', indicating when the break starts.
     * @property string      $end_time             Stored as 'HH:mm:ss', indicating when the break ends.
     * @property int         $user_id
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     */

    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isBreakActive($slotStartTime, $slotEndTime): bool
    {
        $startBreakTime = Carbon::parse($this->start_time);
        $endBreakTime   = Carbon::parse($this->end_time);

        if ($slotStartTime->copy()->greaterThanOrEqualTo($startBreakTime) &&
            $slotEndTime->copy()->lessThanOrEqualTo($endBreakTime)) {

            return true;
        }

        return false;
    }

}

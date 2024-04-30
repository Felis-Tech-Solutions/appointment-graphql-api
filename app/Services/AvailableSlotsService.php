<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\SlotRule;
use App\Models\BreakRule;
use Illuminate\Database\Eloquent\Collection;

class AvailableSlotsService
{
    public function getAvailableSlots(Collection $slotRules, Collection $breakRules, Carbon $referenceDate): array|null
    {
        $groupedAvailableSlots = [];

        $slotRules->each(function (SlotRule $rule) use (&$groupedAvailableSlots, $breakRules, $referenceDate,) {
            $weekMap = [
                'Monday'    => 0,
                'Tuesday'   => 1,
                'Wednesday' => 2,
                'Thursday'  => 3,
                'Friday'    => 4,
            ];

            $dayOfWeek = $rule->day_of_the_week;

            $ruleStartTime = Carbon::parse($rule->start_time);
            $ruleEndTime   = Carbon::parse($rule->end_time);

            $timeAfterAppointment = $rule->time_after_appointment / 60000;
            $totalDuration        = $rule->slot_duration / 60000;

            $availableAppointmentSlots = $ruleStartTime->diffInMinutes($ruleEndTime) /
                                         ($totalDuration + $timeAfterAppointment);

            for ($i = 0; $i < $availableAppointmentSlots; $i++) {

                $slotStartTime = ($i == 0) ? $ruleStartTime : $ruleStartTime->addMinutes($totalDuration +
                                                                                         $timeAfterAppointment);
                if ($slotStartTime->greaterThanOrEqualTo($ruleEndTime)) {
                    break;
                }

                $slotEndTime = $slotStartTime->copy()->addMinutes($totalDuration);

                if ($this->isSlotDuringActiveBreak($breakRules, $dayOfWeek, $slotStartTime, $slotEndTime)) {
                    continue;
                }

                if (! isset($groupedAvailableSlots[$dayOfWeek])) {
                    $groupedAvailableSlots[$dayOfWeek] = [
                        'date'  => $referenceDate->copy()
                            ->startOfWeek()
                            ->addDays($weekMap[$dayOfWeek])
                            ->format('Y-m-d'),
                        'slots' => [],
                    ];
                }

                $groupedAvailableSlots[$dayOfWeek]['slots'][] = [
                    'startTime' => $slotStartTime->format('H:i:s'),
                    'endTime'   => $slotEndTime->format('H:i:s'),
                ];
            }
        });

        return $groupedAvailableSlots;
    }

    private function isSlotDuringActiveBreak($breakRules, $dayOfWeek, $slotStartTime, $slotEndTime): bool
    {
        $breaksForDay = $breakRules->filter(fn ($rule) => $rule->day_of_the_week === $dayOfWeek);

        return $breaksForDay->first(function (BreakRule $break) use ($slotStartTime, $slotEndTime) {
                return $break->isBreakActive($slotStartTime, $slotEndTime);
            }) !== null;
    }
}

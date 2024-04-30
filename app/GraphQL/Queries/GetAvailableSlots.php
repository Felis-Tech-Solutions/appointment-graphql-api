<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use Carbon\Carbon;
use App\Models\User;
use App\Services\AvailableSlotsService;

final class GetAvailableSlots
{
    /** @param array{} $args */
    public function __invoke(null $_, array $args): ?array
    {
        $userId        = $args['user_id'];
        $referenceDate = Carbon::parse($args['reference_date']);

        /* @var User $user */
        $user = User::find($userId);

        if (! $user) {
            return [];
        }

        $slotRules  = $user->slotRules()->get();
        $breakRules = $user->breakRules()->get();

        $availableSlotsService = new AvailableSlotsService();

        $slotsServiceByDay = $availableSlotsService->getAvailableSlots($slotRules, $breakRules, $referenceDate);

        $result = [];
        foreach ($slotsServiceByDay as $day => $data) {
            $result[] = [
                'day'   => $day,
                'date'  => $data['date'],
                'slots' => array_map(function ($slot) {
                    return [
                        'startTime' => $slot['startTime'],
                        'endTime'   => $slot['endTime'],
                    ];
                }, $data['slots']),
            ];
        }
        return $result;
    }
}

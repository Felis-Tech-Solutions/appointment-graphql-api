<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\User;
use App\Services\AvailableSlotsService;

final class GetAvailableSlots
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): ?array
    {
        $userId = $args['user_id'];

        /* @var User $user */
        $user = User::find($userId);

        if (! $user) {
            return [];
        }

        $slotRules  = $user->slotRules()->get();
        $breakRules = $user->breakRules()->get();

        $availableSlotsService = new AvailableSlotsService();

        $slotsServiceByDay =  $availableSlotsService->getAvailableSlots($slotRules, $breakRules);

        $result = [];
        foreach ($slotsServiceByDay as $day => $slots) {
            $result[] = [
                'day' => $day,
                'slots' => array_map(function ($slot) {
                    return [
                        'start_time' => $slot['start_time'],
                        'end_time' => $slot['end_time'],
                    ];
                }, $slots),
            ];
        }

        return $result;
    }
}

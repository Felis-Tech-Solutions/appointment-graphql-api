<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppointmentStatus;

class AppointmentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'Pending',
            'Approved',
            'Cancelled',
            'Completed',
        ];

        foreach ($statuses as $status) {
            AppointmentStatus::updateOrCreate([
                'name' => $status,
            ]);
        }
    }
}

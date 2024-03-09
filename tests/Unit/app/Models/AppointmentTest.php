<?php

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\HasFactory;

test('Appointment model has factory', function () {
    $this->assertContains(
        HasFactory::class,
        class_uses(Appointment::class)
    );
});



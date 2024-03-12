<?php

use App\Models\AppointmentType;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

test('Group model has factory', function () {
    $this->assertContains(
        HasFactory::class,
        class_uses(AppointmentType::class)
    );
});

test('Group model can be soft deleted', function () {
    $this->assertContains(
        softDeletes::class,
        class_uses(AppointmentType::class)
    );
});

<?php

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\HasFactory;

test('Group model has factory', function () {
    $this->assertContains(
        HasFactory::class,
        class_uses(Group::class)
    );
});

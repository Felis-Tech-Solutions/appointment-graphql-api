<?php

use App\Models\Group;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

test('Group model has factory', function () {
    $this->assertContains(
        HasFactory::class,
        class_uses(Group::class)
    );
});

test('Group model can be soft deleted', function () {
    $this->assertContains(
        softDeletes::class,
        class_uses(Group::class)
    );
});

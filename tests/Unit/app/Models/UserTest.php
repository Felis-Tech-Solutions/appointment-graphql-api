<?php

use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

test('User model has factory', function () {
    $this->assertContains(
        HasFactory::class,
        class_uses(User::class)
    );
});

test('User model can be soft deleted', function () {
    $this->assertContains(
        softDeletes::class,
        class_uses(User::class)
    );
});

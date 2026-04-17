<?php

use App\Models\User;

test('user resolves configured session lifetime', function () {
    $user = new User([
        'session_lifetime_minutes' => 240,
    ]);

    expect($user->resolveSessionLifetimeMinutes(120))->toBe(240);
});

test('user falls back to default session lifetime when override is empty', function () {
    $user = new User();

    expect($user->resolveSessionLifetimeMinutes(480))->toBe(480);
});

test('user falls back to default session lifetime when override is too low', function () {
    $user = new User([
        'session_lifetime_minutes' => 1,
    ]);

    expect($user->resolveSessionLifetimeMinutes(480))->toBe(480);
});

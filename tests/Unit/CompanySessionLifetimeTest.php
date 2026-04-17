<?php

use App\Models\Company;

test('company resolves configured session lifetime', function () {
    $company = new Company([
        'session_lifetime_minutes' => 480,
    ]);

    expect($company->resolveSessionLifetimeMinutes())->toBe(480);
});

test('company falls back to default session lifetime when configured value is too low', function () {
    $company = new Company([
        'session_lifetime_minutes' => 1,
    ]);

    expect($company->resolveSessionLifetimeMinutes(120))->toBe(120);
});

test('company falls back to provided default when session lifetime is empty', function () {
    $company = new Company();

    expect($company->resolveSessionLifetimeMinutes(240))->toBe(240);
});

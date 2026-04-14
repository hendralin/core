<?php

use Shetabit\Visitor\Drivers\JenssegersAgent;
use Shetabit\Visitor\Drivers\UAParser;
use Shetabit\Visitor\Resolvers\GeoIp\NullResolver;
use Shetabit\Visitor\Resolvers\GeoIp\SteveBaumanResolver;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Driver
    |--------------------------------------------------------------------------
    */
    'default' => 'jenssegers',

    /*
    | Except route path patterns (matched via Request::is()).
    | Reduces noise from Livewire updates, auth, and static assets.
    */
    'except' => [
        'login',
        'register',
        'livewire/*',
        'sanctum/*',
        '_debugbar/*',
        'broadcasting/*',
    ],

    'table_name' => 'shetabit_visits',

    'drivers' => [
        'jenssegers' => JenssegersAgent::class,
        'UAParser' => UAParser::class,
    ],

    'geoip' => false,
    'resolver' => 'null',
    'resolvers' => [
        'stevebauman' => SteveBaumanResolver::class,
        'null' => NullResolver::class,
    ],
];

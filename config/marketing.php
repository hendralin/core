<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Marketing WhatsApp contacts (public site)
    |--------------------------------------------------------------------------
    |
    | `phone` is stored in local Indonesian format (commonly starts with 0).
    | Views convert it to wa.me international format (62...) when building links.
    |
    */
    'whatsapp_contacts' => [
        ['name' => 'Fedi', 'phone' => '082186414282'],
        ['name' => 'Viki', 'phone' => '082180350773'],
        ['name' => 'Ikhsan', 'phone' => '085789200738'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default WhatsApp message (footer quick links)
    |--------------------------------------------------------------------------
    */
    'whatsapp_default_message' => 'Halo kak, saya ingin menanyakan tentang kendaraan yang tersedia.',
];

<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Stockbit Snips — GeneralSearch API
    |--------------------------------------------------------------------------
    |
    | Endpoint contoh:
    | https://snips.stockbit.com/api/search/GeneralSearch?crumb=...&q=bca&p=0&size=10
    |
    | Parameter `crumb` biasanya berubah; isi dari browser (DevTools) jika request gagal.
    |
    | `query_aliases`: kode emiten IDX → string `q` untuk GeneralSearch. Contoh: API memakai
    | q=bca untuk konten Bank BCA (BBCA), bukan q=bbca.
    |
    */
    'stockbit_snips' => [
        'base_url' => env('STOCKBIT_SNIPS_BASE_URL', 'https://snips.stockbit.com'),
        'crumb' => env('STOCKBIT_SNIPS_CRUMB', ''),
        /*
         * Verifikasi SSL untuk request HTTPS. Di Windows tanpa CA bundle lengkap sering
         * gagal (cURL error 60); set false hanya untuk lokal / troubleshooting.
         */
        'verify_ssl' => filter_var(env('STOCKBIT_SNIPS_VERIFY_SSL', true), FILTER_VALIDATE_BOOL),
        'query_aliases' => [
            'BBCA' => 'bca',
        ],
    ],
];

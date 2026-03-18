<?php

return [
    'tables' => [
        'financial_ratios' => [
            'description' => 'Ringkasan rasio keuangan per emiten dan periode laporan.',
            'columns' => [
                'code' => 'Kode emiten (mis. BBCA).',
                'fs_date' => 'Tanggal laporan keuangan.',
                'sector' => 'Sektor IDX.',
                'industry' => 'Industri IDX.',
                'sharia' => 'Status syariah (S = syariah).',
                'per' => 'Price to Earnings Ratio.',
                'price_bv' => 'Price to Book Value.',
                'de_ratio' => 'Debt to Equity ratio.',
                'roe' => 'Return on Equity.',
                'roa' => 'Return on Assets.',
                'npm' => 'Net Profit Margin.',
            ],
        ],
        'stock_companies' => [
            'description' => 'Profil emiten yang tercatat di Bursa Efek Indonesia.',
            'columns' => [
                'kode_emiten' => 'Kode emiten.',
                'nama_emiten' => 'Nama perusahaan.',
                'sektor' => 'Sektor IDX.',
                'industri' => 'Industri IDX.',
                'kegiatan_usaha_utama' => 'Deskripsi singkat kegiatan usaha utama.',
                'papan_pencatatan' => 'Papan pencatatan (Utama, Pengembangan, dsb.).',
            ],
        ],
    ],
];


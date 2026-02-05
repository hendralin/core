<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Categories & tags untuk fitur blog/berita aplikasi saham (bandar-saham).
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Berita Pasar',
                'description' => 'Berita terkini pergerakan pasar saham, IHSG, dan bursa global',
                'color' => '#2563eb', // blue
            ],
            [
                'name' => 'Analisis Saham',
                'description' => 'Analisis fundamental dan teknikal saham emiten',
                'color' => '#059669', // emerald
            ],
            [
                'name' => 'Laporan Emiten',
                'description' => 'Laporan keuangan, kinerja perusahaan, dan corporate action',
                'color' => '#7c3aed', // violet
            ],
            [
                'name' => 'Investasi & Edukasi',
                'description' => 'Tips investasi, edukasi pasar modal, dan literasi keuangan',
                'color' => '#d97706', // amber
            ],
            [
                'name' => 'Reksa Dana',
                'description' => 'Berita dan ulasan reksa dana serta produk investasi kolektif',
                'color' => '#0891b2', // cyan
            ],
            [
                'name' => 'Ekonomi Makro',
                'description' => 'Kebijakan BI, inflasi, suku bunga, dan kondisi ekonomi',
                'color' => '#dc2626', // red
            ],
            [
                'name' => 'IPO & Rights Issue',
                'description' => 'Penawaran umum perdana dan hak memesan efek terlebih dahulu',
                'color' => '#db2777', // pink
            ],
            [
                'name' => 'Dividen & Corporate Action',
                'description' => 'Pembagian dividen, stock split, dan aksi korporasi lainnya',
                'color' => '#65a30d', // lime
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($category['name'])],
                $category
            );
        }

        $tags = [
            'Saham',
            'IHSG',
            'LQ45',
            'IPO',
            'Dividen',
            'Fundamental',
            'Teknikal',
            'Reksa Dana',
            'Market Update',
            'Emiten',
            'Laporan Keuangan',
            'Capital Gain',
            'Rights Issue',
            'Stock Split',
            'Bursa Efek Indonesia',
            'Sektor Perbankan',
            'Sektor Teknologi',
            'Sektor Konsumer',
            'Trading',
            'Investasi Jangka Panjang',
            'Edukasi Pasar Modal',
            'BI Rate',
            'Inflasi',
            'Signal',
        ];

        foreach ($tags as $tagName) {
            Tag::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($tagName)],
                ['name' => $tagName]
            );
        }
    }
}

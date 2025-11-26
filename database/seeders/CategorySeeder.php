<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate the table to start fresh
        Category::truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = [
            [
                'name' => 'MB - Mobil Penumpang',
                'description' => 'Mobil penumpang umum untuk mengangkut orang, termasuk sedan, hatchback, MPV, SUV, dan coupe.'
            ],
            [
                'name' => 'BB - Mobil Barang',
                'description' => 'Mobil barang untuk mengangkut barang dengan berat tertentu, termasuk pickup, truck, dan van.'
            ],
            [
                'name' => 'BA - Bus Angkutan Orang',
                'description' => 'Bus untuk mengangkut penumpang dalam jumlah banyak, digunakan untuk transportasi umum.'
            ],
            [
                'name' => 'BK - Bus Khusus',
                'description' => 'Bus khusus untuk keperluan tertentu seperti bus pariwisata, bus sekolah, atau bus perusahaan.'
            ],
            [
                'name' => 'TK - Mobil Taksi',
                'description' => 'Mobil taksi untuk jasa transportasi penumpang dengan tarif meteran atau online.'
            ],
            [
                'name' => 'DS - Mobil Dinas',
                'description' => 'Mobil dinas untuk keperluan pemerintah, instansi, atau perusahaan swasta.'
            ],
            [
                'name' => 'KH - Kendaraan Khusus',
                'description' => 'Kendaraan khusus seperti mobil pemadam kebakaran, ambulans, mobil jenazah, dan kendaraan khusus lainnya.'
            ],
            [
                'name' => 'BC - Mobil Barang Campuran',
                'description' => 'Mobil yang dapat digunakan untuk mengangkut barang dan penumpang secara bersamaan.'
            ],
            [
                'name' => 'BU - Bus Umum',
                'description' => 'Bus umum untuk transportasi massal penumpang dalam kota atau antar kota.'
            ],
            [
                'name' => 'KB - Kendaraan Bermotor Khusus',
                'description' => 'Kendaraan bermotor khusus seperti mobil pemadam kebakaran, ambulans, dan kendaraan darurat lainnya.'
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}

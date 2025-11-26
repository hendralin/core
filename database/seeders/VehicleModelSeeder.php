<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VehicleModel;

class VehicleModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicleModels = [
            [
                'name' => 'SEDAN',
                'description' => 'Kendaraan penumpang dengan 4 pintu, bagasi terpisah, dan desain elegan untuk penggunaan sehari-hari.'
            ],
            [
                'name' => 'HATCHBACK',
                'description' => 'Mobil kompak dengan pintu belakang terintegrasi, praktis untuk penggunaan perkotaan.'
            ],
            [
                'name' => 'STATION WAGON',
                'description' => 'Mobil keluarga dengan ruang kargo yang luas dan fleksibel untuk berbagai keperluan.'
            ],
            [
                'name' => 'SUV',
                'description' => 'Sport Utility Vehicle dengan ground clearance tinggi, cocok untuk berbagai kondisi jalan.'
            ],
            [
                'name' => 'MPV',
                'description' => 'Multi Purpose Vehicle dengan kapasitas penumpang banyak dan ruang yang luas.'
            ],
            [
                'name' => 'MINIVAN',
                'description' => 'Mobil kecil dengan kapasitas penumpang sedang, praktis untuk keluarga kecil.'
            ],
            [
                'name' => 'PICK UP',
                'description' => 'Kendaraan niaga dengan bak terbuka di belakang untuk mengangkut barang.'
            ],
            [
                'name' => 'TRUCK',
                'description' => 'Kendaraan niaga berat untuk mengangkut barang dalam jumlah besar.'
            ],
            [
                'name' => 'BUS',
                'description' => 'Kendaraan umum untuk mengangkut banyak penumpang dalam perjalanan jauh.'
            ],
            [
                'name' => 'MINIBUS',
                'description' => 'Bus kecil untuk mengangkut penumpang dalam jumlah sedang, umumnya untuk pariwisata.'
            ],
            [
                'name' => 'MICR BUS',
                'description' => 'Bus mikro dengan kapasitas penumpang kecil untuk rute pendek.'
            ],
            [
                'name' => 'VAN',
                'description' => 'Kendaraan niaga untuk mengangkut barang atau penumpang dalam jumlah sedang.'
            ],
            [
                'name' => 'PANEL VAN',
                'description' => 'Van dengan bodi tertutup untuk mengangkut barang atau peralatan khusus.'
            ],
            [
                'name' => 'JEEP',
                'description' => 'Kendaraan off-road dengan kemampuan tinggi untuk medan berat.'
            ],
            [
                'name' => 'COUPE',
                'description' => 'Mobil sport dengan 2 pintu dan desain aerodinamis untuk performa tinggi.'
            ],
            [
                'name' => 'CONVERTIBLE',
                'description' => 'Mobil dengan atap terbuka yang dapat dilipat, cocok untuk cuaca tropis.'
            ],
            [
                'name' => 'ROADSTER',
                'description' => 'Mobil sport ringan dengan 2 tempat duduk dan performa tinggi.'
            ],
            [
                'name' => 'CROSSOVER',
                'description' => 'Kendaraan yang menggabungkan fitur sedan dan SUV dengan efisiensi bahan bakar.'
            ],
            [
                'name' => 'BOX TRUCK',
                'description' => 'Truk dengan bak tertutup untuk mengangkut barang dalam kondisi aman.'
            ],
            [
                'name' => 'DUMP TRUCK',
                'description' => 'Truk dengan bak yang dapat dimiringkan untuk membuang material.'
            ],
            [
                'name' => 'TRACTOR HEAD',
                'description' => 'Kepala truk untuk menarik trailer dalam transportasi berat.'
            ],
            [
                'name' => 'SEMI TRAILER',
                'description' => 'Trailer sebagian yang ditarik oleh tractor head.'
            ],
            [
                'name' => 'FULL TRAILER',
                'description' => 'Trailer lengkap yang dapat ditarik oleh kendaraan.'
            ],
            [
                'name' => 'WAGON',
                'description' => 'Mobil dengan desain klasik dan ruang kargo yang luas.'
            ],
            [
                'name' => 'COUPE SUV',
                'description' => 'SUV dengan desain coupe yang sporty dan aerodinamis.'
            ],
        ];

        foreach ($vehicleModels as $vehicleModel) {
            VehicleModel::create($vehicleModel);
        }
    }
}

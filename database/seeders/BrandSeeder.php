<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Toyota',
                'description' => 'Produsen mobil terbesar di dunia dan sangat populer di Indonesia dengan berbagai model seperti Avanza, Innova, dan Fortuner.'
            ],
            [
                'name' => 'Honda',
                'description' => 'Merek mobil Jepang yang dikenal dengan teknologi canggih dan model seperti Jazz, Civic, dan CR-V.'
            ],
            [
                'name' => 'Mitsubishi',
                'description' => 'Produsen mobil asal Jepang dengan model SUV seperti Pajero, Triton, dan Xpander yang sangat populer di Indonesia.'
            ],
            [
                'name' => 'Suzuki',
                'description' => 'Merek mobil Jepang yang fokus pada mobil kecil dan terjangkau seperti Ertiga, Carry, dan Karimun Wagon.'
            ],
            [
                'name' => 'Nissan',
                'description' => 'Produsen mobil Jepang dengan model seperti Grand Livina, Evalia, dan Terra yang populer di Indonesia.'
            ],
            [
                'name' => 'Daihatsu',
                'description' => 'Merek mobil Jepang yang khusus memproduksi mobil kecil seperti Ayla, Sigra, dan Terios.'
            ],
            [
                'name' => 'BMW',
                'description' => 'Merek mobil mewah Jerman yang dikenal dengan performa tinggi dan teknologi canggih.'
            ],
            [
                'name' => 'Mercedes-Benz',
                'description' => 'Merek mobil mewah asal Jerman dengan reputasi kelas dunia dalam hal kualitas dan inovasi.'
            ],
            [
                'name' => 'Volkswagen',
                'description' => 'Produsen mobil Jerman yang populer dengan model seperti Tiguan, Polo, dan Golf.'
            ],
            [
                'name' => 'Ford',
                'description' => 'Merek mobil Amerika yang dikenal dengan model seperti Ranger, Everest, dan EcoSport.'
            ],
            [
                'name' => 'Chevrolet',
                'description' => 'Merek mobil Amerika dengan model seperti Trailblazer, Captiva, dan Spin.'
            ],
            [
                'name' => 'Hyundai',
                'description' => 'Produsen mobil Korea Selatan dengan model seperti Creta, Tucson, dan Santa Fe.'
            ],
            [
                'name' => 'Kia',
                'description' => 'Merek mobil Korea Selatan yang menawarkan berbagai model seperti Sportage, Sorento, dan Carnival.'
            ],
            [
                'name' => 'Mazda',
                'description' => 'Produsen mobil Jepang yang fokus pada desain dan performa dengan model seperti CX-5 dan CX-9.'
            ],
            [
                'name' => 'Isuzu',
                'description' => 'Merek mobil komersial yang populer dengan truk dan pickup seperti Isuzu Panther dan Traga.'
            ],
            [
                'name' => 'Hino',
                'description' => 'Produsen truk dan bus asal Jepang yang sangat populer untuk keperluan komersial.'
            ],
            [
                'name' => 'UD Trucks',
                'description' => 'Merek truk berat asal Jepang yang dikenal dengan ketahanan dan performa tinggi.'
            ],
            [
                'name' => 'Volvo',
                'description' => 'Merek mobil Swedia yang fokus pada keselamatan dan keandalan dengan model seperti XC90 dan XC60.'
            ],
            [
                'name' => 'Scania',
                'description' => 'Produsen truk berat asal Swedia yang terkenal dengan performa dan efisiensi bahan bakar.'
            ],
            [
                'name' => 'Wuling',
                'description' => 'Merek mobil Cina yang mulai populer di Indonesia dengan model seperti Confero dan Cortez.'
            ],
            [
                'name' => 'DFSK',
                'description' => 'Produsen mobil komersial asal Cina dengan model seperti Glory 560 dan Super Cab.'
            ],
            [
                'name' => 'Chery',
                'description' => 'Merek mobil Cina yang menawarkan berbagai model terjangkau seperti Tiggo dan QQ.'
            ],
            [
                'name' => 'Geely',
                'description' => 'Produsen mobil Cina yang mulai berkembang di Indonesia dengan berbagai model SUV.'
            ],
            [
                'name' => 'Audi',
                'description' => 'Merek mobil mewah Jerman bagian dari Volkswagen Group dengan fokus pada teknologi premium.'
            ],
            [
                'name' => 'Porsche',
                'description' => 'Merek mobil sport mewah asal Jerman yang terkenal dengan performa tinggi.'
            ],
            [
                'name' => 'Lexus',
                'description' => 'Merek mobil mewah Toyota yang menawarkan kualitas premium dan teknologi canggih.'
            ],
            [
                'name' => 'Infiniti',
                'description' => 'Merek mobil mewah Nissan dengan desain elegan dan performa tinggi.'
            ],
            [
                'name' => 'Jaguar',
                'description' => 'Merek mobil mewah Inggris yang terkenal dengan desain elegan dan performa sporty.'
            ],
            [
                'name' => 'Land Rover',
                'description' => 'Merek SUV mewah Inggris yang sangat cocok untuk berbagai kondisi jalan.'
            ],
            [
                'name' => 'Jeep',
                'description' => 'Merek SUV Amerika yang legendaris dengan kemampuan off-road yang luar biasa.'
            ],
            [
                'name' => 'Tesla',
                'description' => 'Produsen mobil listrik Amerika yang revolusioner dengan teknologi autonomous driving.'
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}

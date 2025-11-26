<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;
use App\Models\Type;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get brand IDs
        $brands = Brand::pluck('id', 'name')->toArray();

        $types = [
            // TOYOTA
            [
                'brand_id' => $brands['Toyota'],
                'name' => 'AVANZA 1.3 G MT',
                'description' => 'Toyota Avanza 1300cc Gasoline Manual Transmission - MPV 7-seater populer untuk keluarga.'
            ],
            [
                'brand_id' => $brands['Toyota'],
                'name' => 'AVANZA 1.3 G AT',
                'description' => 'Toyota Avanza 1300cc Gasoline Automatic Transmission - MPV 7-seater dengan transmisi otomatis.'
            ],
            [
                'brand_id' => $brands['Toyota'],
                'name' => 'INNOVA 2.0 G AT',
                'description' => 'Toyota Innova 2000cc Gasoline Automatic Transmission - MPV premium 8-seater.'
            ],
            [
                'brand_id' => $brands['Toyota'],
                'name' => 'INNOVA 2.4 V AT',
                'description' => 'Toyota Innova 2400cc Gasoline Automatic Transmission - MPV premium dengan mesin VVT-i.'
            ],
            [
                'brand_id' => $brands['Toyota'],
                'name' => 'FORTUNER 2.4 G AT',
                'description' => 'Toyota Fortuner 2400cc Gasoline Automatic Transmission - SUV tangguh untuk berbagai medan.'
            ],
            [
                'brand_id' => $brands['Toyota'],
                'name' => 'FORTUNER 2.8 D AT',
                'description' => 'Toyota Fortuner 2800cc Diesel Automatic Transmission - SUV diesel dengan tenaga besar.'
            ],
            [
                'brand_id' => $brands['Toyota'],
                'name' => 'KIJANG INNOVA 2.0 V AT',
                'description' => 'Toyota Kijang Innova 2000cc Gasoline Automatic Transmission - MPV legendaris Indonesia.'
            ],
            [
                'brand_id' => $brands['Toyota'],
                'name' => 'CALYA 1.2 G MT',
                'description' => 'Toyota Calya 1200cc Gasoline Manual Transmission - Sedan hatchback untuk keluarga muda.'
            ],
            [
                'brand_id' => $brands['Toyota'],
                'name' => 'AGYA 1.0 G MT',
                'description' => 'Toyota Agya 1000cc Gasoline Manual Transmission - City car ekonomis.'
            ],

            // HONDA
            [
                'brand_id' => $brands['Honda'],
                'name' => 'JAZZ 1.5 RS CVT',
                'description' => 'Honda Jazz 1500cc CVT Transmission - Hatchback premium dengan fitur keselamatan lengkap.'
            ],
            [
                'brand_id' => $brands['Honda'],
                'name' => 'JAZZ 1.5 S MT',
                'description' => 'Honda Jazz 1500cc Manual Transmission - Hatchback dengan handling sporty.'
            ],
            [
                'brand_id' => $brands['Honda'],
                'name' => 'CIVIC 1.5 TC AT',
                'description' => 'Honda Civic 1500cc Turbocharged Automatic Transmission - Sedan sport dengan mesin turbo.'
            ],
            [
                'brand_id' => $brands['Honda'],
                'name' => 'CR-V 1.5 TC AT',
                'description' => 'Honda CR-V 1500cc Turbocharged Automatic Transmission - SUV premium dengan mesin turbo.'
            ],
            [
                'brand_id' => $brands['Honda'],
                'name' => 'HR-V 1.5 RS CVT',
                'description' => 'Honda HR-V 1500cc CVT Transmission - Crossover SUV dengan desain modern.'
            ],
            [
                'brand_id' => $brands['Honda'],
                'name' => 'BRIO 1.2 RS CVT',
                'description' => 'Honda Brio 1200cc CVT Transmission - City car dengan fitur premium.'
            ],
            [
                'brand_id' => $brands['Honda'],
                'name' => 'BR-V 1.5 E CVT',
                'description' => 'Honda BR-V 1500cc CVT Transmission - MPV crossover 7-seater.'
            ],

            // MITSUBISHI
            [
                'brand_id' => $brands['Mitsubishi'],
                'name' => 'XPANDER 1.5 MT',
                'description' => 'Mitsubishi Xpander 1500cc Manual Transmission - MPV 7-seater dengan ground clearance tinggi.'
            ],
            [
                'brand_id' => $brands['Mitsubishi'],
                'name' => 'XPANDER 1.5 AT',
                'description' => 'Mitsubishi Xpander 1500cc Automatic Transmission - MPV otomatis dengan fitur keselamatan.'
            ],
            [
                'brand_id' => $brands['Mitsubishi'],
                'name' => 'XPANDER CROSS 1.5 AT',
                'description' => 'Mitsubishi Xpander Cross 1500cc Automatic Transmission - MPV crossover dengan desain sporty.'
            ],
            [
                'brand_id' => $brands['Mitsubishi'],
                'name' => 'PAJERO SPORT 2.4 D AT',
                'description' => 'Mitsubishi Pajero Sport 2400cc Diesel Automatic Transmission - SUV off-road tangguh.'
            ],
            [
                'brand_id' => $brands['Mitsubishi'],
                'name' => 'TRITON 2.4 D MT',
                'description' => 'Mitsubishi Triton 2400cc Diesel Manual Transmission - Pickup double cabin.'
            ],
            [
                'brand_id' => $brands['Mitsubishi'],
                'name' => 'TRITON 2.4 D AT',
                'description' => 'Mitsubishi Triton 2400cc Diesel Automatic Transmission - Pickup dengan transmisi otomatis.'
            ],
            [
                'brand_id' => $brands['Mitsubishi'],
                'name' => 'OUTLANDER SPORT 2.0 G CVT',
                'description' => 'Mitsubishi Outlander Sport 2000cc CVT Transmission - Crossover SUV kompak.'
            ],

            // SUZUKI
            [
                'brand_id' => $brands['Suzuki'],
                'name' => 'ERTIGA 1.5 GX MT',
                'description' => 'Suzuki Ertiga 1500cc Manual Transmission - MPV 7-seater ekonomis.'
            ],
            [
                'brand_id' => $brands['Suzuki'],
                'name' => 'ERTIGA 1.5 GX AT',
                'description' => 'Suzuki Ertiga 1500cc Automatic Transmission - MPV otomatis dengan fitur lengkap.'
            ],
            [
                'brand_id' => $brands['Suzuki'],
                'name' => 'CARRY 1.5 D MT',
                'description' => 'Suzuki Carry 1500cc Diesel Manual Transmission - Pickup box untuk keperluan komersial.'
            ],
            [
                'brand_id' => $brands['Suzuki'],
                'name' => 'CARRY 1.5 D AT',
                'description' => 'Suzuki Carry 1500cc Diesel Automatic Transmission - Pickup box dengan transmisi otomatis.'
            ],
            [
                'brand_id' => $brands['Suzuki'],
                'name' => 'APV 1.5 GX MT',
                'description' => 'Suzuki APV 1500cc Manual Transmission - MPV komersial untuk angkutan penumpang.'
            ],
            [
                'brand_id' => $brands['Suzuki'],
                'name' => 'IGNIS 1.2 GX MT',
                'description' => 'Suzuki Ignis 1200cc Manual Transmission - Hatchback crossover dengan ground clearance tinggi.'
            ],
            [
                'brand_id' => $brands['Suzuki'],
                'name' => 'KARIMUN WAGON 1.2 R MT',
                'description' => 'Suzuki Karimun Wagon 1200cc Manual Transmission - MPV untuk keperluan komersial.'
            ],

            // DAIHATSU
            [
                'brand_id' => $brands['Daihatsu'],
                'name' => 'SIGRA 1.0 D MT',
                'description' => 'Daihatsu Sigra 1000cc Dual Transmission Manual - Hatchback 7-seater ekonomis.'
            ],
            [
                'brand_id' => $brands['Daihatsu'],
                'name' => 'SIGRA 1.0 D AT',
                'description' => 'Daihatsu Sigra 1000cc Dual Transmission Automatic - Hatchback 7-seater otomatis.'
            ],
            [
                'brand_id' => $brands['Daihatsu'],
                'name' => 'AYLA 1.0 X MT',
                'description' => 'Daihatsu Ayla 1000cc Manual Transmission - City car hatchback ekonomis.'
            ],
            [
                'brand_id' => $brands['Daihatsu'],
                'name' => 'AYLA 1.0 X AT',
                'description' => 'Daihatsu Ayla 1000cc Automatic Transmission - City car hatchback otomatis.'
            ],
            [
                'brand_id' => $brands['Daihatsu'],
                'name' => 'TERIOS 1.5 X MT',
                'description' => 'Daihatsu Terios 1500cc Manual Transmission - SUV kompak untuk keluarga.'
            ],
            [
                'brand_id' => $brands['Daihatsu'],
                'name' => 'TERIOS 1.5 X AT',
                'description' => 'Daihatsu Terios 1500cc Automatic Transmission - SUV kompak dengan transmisi otomatis.'
            ],
            [
                'brand_id' => $brands['Daihatsu'],
                'name' => 'GRAN MAX 1.3 D MT',
                'description' => 'Daihatsu Gran Max 1300cc Diesel Manual Transmission - Pickup box untuk keperluan komersial.'
            ],
            [
                'brand_id' => $brands['Daihatsu'],
                'name' => 'GRAN MAX 1.3 D AT',
                'description' => 'Daihatsu Gran Max 1300cc Diesel Automatic Transmission - Pickup box dengan transmisi otomatis.'
            ],

            // NISSAN
            [
                'brand_id' => $brands['Nissan'],
                'name' => 'GRAND LIVINA 1.5 XV AT',
                'description' => 'Nissan Grand Livina 1500cc Automatic Transmission - MPV 8-seater premium.'
            ],
            [
                'brand_id' => $brands['Nissan'],
                'name' => 'EVALIA 1.5 XV AT',
                'description' => 'Nissan Evalia 1500cc Automatic Transmission - MPV untuk keperluan komersial.'
            ],
            [
                'brand_id' => $brands['Nissan'],
                'name' => 'TERRA 2.5 D AT',
                'description' => 'Nissan Terra 2500cc Diesel Automatic Transmission - SUV 7-seater tangguh.'
            ],
            [
                'brand_id' => $brands['Nissan'],
                'name' => 'NAVARA 2.5 D AT',
                'description' => 'Nissan Navara 2500cc Diesel Automatic Transmission - Pickup double cabin.'
            ],
            [
                'brand_id' => $brands['Nissan'],
                'name' => 'SERENA 2.0 XV AT',
                'description' => 'Nissan Serena 2000cc Automatic Transmission - MPV premium 8-seater.'
            ],

            // WULING
            [
                'brand_id' => $brands['Wuling'],
                'name' => 'CONFERO 1.5 S MT',
                'description' => 'Wuling Confero 1500cc Manual Transmission - MPV 7-seater ekonomis.'
            ],
            [
                'brand_id' => $brands['Wuling'],
                'name' => 'CONFERO 1.5 S AT',
                'description' => 'Wuling Confero 1500cc Automatic Transmission - MPV 7-seater otomatis.'
            ],
            [
                'brand_id' => $brands['Wuling'],
                'name' => 'CORTEZ 1.5 CT AT',
                'description' => 'Wuling Cortez 1500cc Automatic Transmission - SUV crossover 7-seater.'
            ],

            // DFSK
            [
                'brand_id' => $brands['DFSK'],
                'name' => 'GLORY 560 1.5 T MT',
                'description' => 'DFSK Glory 560 1500cc Turbo Manual Transmission - MPV 7-seater.'
            ],
            [
                'brand_id' => $brands['DFSK'],
                'name' => 'SUPER CAB 1.3 D MT',
                'description' => 'DFSK Super Cab 1300cc Diesel Manual Transmission - Pickup single cabin.'
            ],

            // ISUZU
            [
                'brand_id' => $brands['Isuzu'],
                'name' => 'PANTHER 2.5 D MT',
                'description' => 'Isuzu Panther 2500cc Diesel Manual Transmission - Pickup double cabin tangguh.'
            ],
            [
                'brand_id' => $brands['Isuzu'],
                'name' => 'PANTHER 2.5 D AT',
                'description' => 'Isuzu Panther 2500cc Diesel Automatic Transmission - Pickup dengan transmisi otomatis.'
            ],
            [
                'brand_id' => $brands['Isuzu'],
                'name' => 'D-MAX 2.5 D AT',
                'description' => 'Isuzu D-Max 2500cc Diesel Automatic Transmission - Pickup premium double cabin.'
            ],

            // HYUNDAI
            [
                'brand_id' => $brands['Hyundai'],
                'name' => 'CRETA 1.5 AT',
                'description' => 'Hyundai Creta 1500cc Automatic Transmission - SUV crossover kompak.'
            ],
            [
                'brand_id' => $brands['Hyundai'],
                'name' => 'TUCSON 2.0 AT',
                'description' => 'Hyundai Tucson 2000cc Automatic Transmission - SUV premium dengan fitur keselamatan lengkap.'
            ],
            [
                'brand_id' => $brands['Hyundai'],
                'name' => 'H-1 2.5 D MT',
                'description' => 'Hyundai H-1 2500cc Diesel Manual Transmission - MPV untuk keperluan komersial.'
            ],

            // KIA
            [
                'brand_id' => $brands['Kia'],
                'name' => 'SPORTAGE 2.0 AT',
                'description' => 'Kia Sportage 2000cc Automatic Transmission - SUV crossover dengan desain modern.'
            ],
            [
                'brand_id' => $brands['Kia'],
                'name' => 'SORENTO 2.2 D AT',
                'description' => 'Kia Sorento 2200cc Diesel Automatic Transmission - SUV 7-seater premium.'
            ],
            [
                'brand_id' => $brands['Kia'],
                'name' => 'CARNIVAL 2.2 D AT',
                'description' => 'Kia Carnival 2200cc Diesel Automatic Transmission - MPV premium 9-seater.'
            ],

            // FORD
            [
                'brand_id' => $brands['Ford'],
                'name' => 'RANGER 2.0 D AT',
                'description' => 'Ford Ranger 2000cc Diesel Automatic Transmission - Pickup double cabin tangguh.'
            ],
            [
                'brand_id' => $brands['Ford'],
                'name' => 'EVEREST 2.0 D AT',
                'description' => 'Ford Everest 2000cc Diesel Automatic Transmission - SUV 7-seater dengan kemampuan off-road.'
            ],

            // CHEVROLET
            [
                'brand_id' => $brands['Chevrolet'],
                'name' => 'TRAILBLAZER 2.5 D AT',
                'description' => 'Chevrolet Trailblazer 2500cc Diesel Automatic Transmission - SUV 7-seater.'
            ],
            [
                'brand_id' => $brands['Chevrolet'],
                'name' => 'CAPTIVA 2.4 G AT',
                'description' => 'Chevrolet Captiva 2400cc Gasoline Automatic Transmission - SUV crossover.'
            ],

            // BMW
            [
                'brand_id' => $brands['BMW'],
                'name' => 'X1 SDRIVE18I AT',
                'description' => 'BMW X1 sDrive18i Automatic Transmission - Luxury compact SUV.'
            ],
            [
                'brand_id' => $brands['BMW'],
                'name' => 'X3 XDRIVE20I AT',
                'description' => 'BMW X3 xDrive20i Automatic Transmission - Luxury SUV mid-size.'
            ],

            // MERCEDES-BENZ
            [
                'brand_id' => $brands['Mercedes-Benz'],
                'name' => 'GLC 200 AT',
                'description' => 'Mercedes-Benz GLC 200 Automatic Transmission - Luxury SUV dengan teknologi canggih.'
            ],
            [
                'brand_id' => $brands['Mercedes-Benz'],
                'name' => 'GLE 300 D AT',
                'description' => 'Mercedes-Benz GLE 300d Automatic Transmission - Luxury SUV diesel.'
            ],
        ];

        foreach ($types as $type) {
            Type::create($type);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Size;
use App\Models\Variant;
use Illuminate\Database\Seeder;

class VariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = [
            'Bodywear' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
            'Waist Size' => ['28', '30', '32', '34', '36', '38', '40'],
            'Foot Wear' => ['6', '7'.'8', '9', '10', '11', '12'],
        ];

        foreach ($sizes as $sizeName => $values) {
            $size = Size::where('name', $sizeName)->first();
            if (! $size) {
                continue;
            }

            foreach ($values as $value) {
                Variant::firstOrCreate([
                    'size_id' => $size->id,
                    'value' => $value,
                ]);
            }
        }
    }
}

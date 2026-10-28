<?php

namespace Database\Seeders;

use App\Models\Audience;
use Illuminate\Database\Seeder;

class AudienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Men', 'Women', 'Unisex'] as $name) {
            Audience::firstOrCreate(['name' => $name]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\House;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class HouseSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = database_path('seeders/bl_house_codes.csv');

        if (! File::exists($csvPath)) {
            $this->command->error("CSV file not found at: {$csvPath}");

            return;
        }

        $lines = file($csvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $codes = array_map(fn ($code) => strtoupper(trim($code)), $lines);

        foreach ($codes as $code) {
            House::firstOrCreate(['code' => $code]);
        }

        $this->command->info('Seeded '.count($codes).' house codes.');
    }
}

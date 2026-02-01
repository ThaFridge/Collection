<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApiProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            ['slug' => 'rawg', 'name' => 'RAWG', 'is_active' => false, 'priority' => 10],
            ['slug' => 'igdb', 'name' => 'IGDB (Twitch)', 'is_active' => false, 'priority' => 20],
            ['slug' => 'rebrickable', 'name' => 'Rebrickable', 'is_active' => false, 'priority' => 10],
            ['slug' => 'brickset', 'name' => 'BrickSet', 'is_active' => false, 'priority' => 20],
        ];

        foreach ($providers as $provider) {
            DB::table('api_providers')->updateOrInsert(
                ['slug' => $provider['slug']],
                array_merge($provider, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}

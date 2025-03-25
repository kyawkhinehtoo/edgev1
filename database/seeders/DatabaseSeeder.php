<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            IspSeeder::class,
            PackageSeeder::class,
            DnPortsSeeder::class,
            CustomerSeeder::class,
        ]);
    }
}

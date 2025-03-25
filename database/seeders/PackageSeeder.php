<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => '10 to 30 Mbps',
                'speed' => '30',
                'installation_timeline' => '48',
                'type' => 'ftth',
                'status' => 1,
                'sla_id' => 1,
                'price' => '10000',
                'contract_period' => 12,
                'currency' => 'mmk',
                'otc' => '100000',
            ],
            [
                'name' => '30 to 50 Mbps',
                'speed' => '50',
                'installation_timeline' => '48',
                'type' => 'ftth',
                'status' => 1,
                'sla_id' => 1,
                'price' => '15000',
                'contract_period' => 12,
                'currency' => 'mmk',
                'otc' => '100000',
            ],
            [
                'name' => '50 to 100 Mbps',
                'speed' => '100',
                'installation_timeline' => '48',
                'type' => 'ftth',
                'status' => 1,
                'sla_id' => 2,
                'price' => '25000',
                'contract_period' => 12,
                'currency' => 'mmk',
                'otc' => '100000',
            ],
            [
                'name' => '100 to 200 Mbps',
                'speed' => '200',
                'installation_timeline' => '48',
                'type' => 'ftth',
                'status' => 1,
                'sla_id' => 3,
                'price' => '40000',
                'contract_period' => 12,
                'currency' => 'mmk',
                'otc' => '100000',
            ],
        ];


        foreach ($packages as $package) {
            Package::create($package);
        }
    }
}

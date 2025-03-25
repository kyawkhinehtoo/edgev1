<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use Carbon\Carbon;
use Faker\Factory as Faker;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $usedCombinations = [];
        
        for ($i = 1; $i <= 50; $i++) {
            $installationDate = Carbon::now()->subDays(rand(30, 365));
            $orderDate = Carbon::parse($installationDate)->subDays(rand(2, 5));
            $activationDate = Carbon::parse($installationDate)->addDays(1);
            
            $gponOnuId = rand(0, 127);
            $gponOnuIdJson = 'OnuID' . $gponOnuId;

            // Generate unique combination
            do {
                $dn_id = rand(1, 2);
                $sn_ports = \App\Models\SnPorts::where('dn_id', $dn_id)->pluck('id')->toArray();
                $pop_device_id = \App\Models\DnPorts::where('id', $dn_id)->pluck('pop_device_id')->first();
                $pop_id = \App\Models\PopDevice::where('id', $pop_device_id)->pluck('pop_id')->first();
                $sn_id = !empty($sn_ports) ? $sn_ports[array_rand($sn_ports)] : null;
                $splitter_id = rand(1, 17);
                $splitter_no = 'SN Port '.$splitter_id;
                $combination = $dn_id . '-' . $sn_id . '-' . $splitter_no;
            } while (in_array($combination, $usedCombinations));
            
            $usedCombinations[] = $combination;

            Customer::create([
                'ftth_id' => 'FTTH-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'name' => $faker->name,
                'phone_1' => '09' . $faker->numberBetween(100000000, 999999999),
                'address' => $faker->address,
                'location' => $faker->latitude(16.0, 22.0) . ',' . $faker->longitude(94.0, 99.0),
                'order_date' => $orderDate,
                'installation_date' => $installationDate,
                'subcom_assign_date' => $installationDate,
                'prefer_install_date' => $installationDate,
                'service_activation_date' => $activationDate,
                'township_id' => 2,
                'package_id' => rand(1, 4),
                'status_id' => 2,
                'subcom_id' => 1,
                'isp_id' => rand(1, 3),
                'partner_id' => 2,
                'installation_status' => 'completed',
                'fiber_distance' => $faker->randomFloat(2, 0.3, 2.0),
                'onu_serial' => 'ONU' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'onu_power' => $faker->randomFloat(2, -25, -15),
                'splitter_no' => $splitter_no,
                'gpon_ontid' => $gponOnuIdJson,
                'created_at' => $orderDate,
                'updated_at' => $activationDate,
                'pop_device_id' => $pop_device_id,
                'pop_id' => $pop_id,
                'sn_id' => $sn_id,
                'dn_id' => $dn_id,
            ]);
        }
    }
}
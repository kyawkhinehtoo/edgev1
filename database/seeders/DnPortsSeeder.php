<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DnPorts;
use App\Models\SnPorts;
use Carbon\Carbon;
use Faker\Factory as Faker;
class DnPortsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 1; $i <= 4; $i++) {
          $dn =  DnPorts::create([
                'name' => 'DN-'.$i,
                'description' => 'Central DN-PORT-'. str_pad($i, 2, '0', STR_PAD_LEFT),
                'pop' => 1,
                'pop_device_id' => 1,
                'gpon_frame' => 1,
                'gpon_slot' => 1,
                'gpon_port' => $i,
                'location' => $faker->latitude(16.0, 22.0) . ',' . $faker->longitude(94.0, 99.0),
                'input_dbm' => $faker->randomFloat(2, 10, 30),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            for ($j = 1; $j <= 4; $j++) {
                SnPorts::create([
                    'name' => 'SN-' . $j,
                    'dn_id' => $dn->id,
                    'description' => 'Central DN-'.$i.'/SN-'. $j,
                    'location' => $faker->latitude(16.0, 22.0) . ',' . $faker->longitude(94.0, 99.0),
                    'input_dbm' => $faker->randomFloat(2, 10, 30),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
        for ($i = 1; $i <= 4; $i++) {
            $dn =  DnPorts::create([
                  'name' => 'DN-'.$i,
                  'description' => 'C45 DN-PORT-'. str_pad($i, 2, '0', STR_PAD_LEFT),
                  'pop' => 2,
                  'pop_device_id' => 1,
                  'gpon_frame' => 1,
                  'gpon_slot' => 1,
                  'gpon_port' => $i,
                  'location' => $faker->latitude(16.0, 22.0) . ',' . $faker->longitude(94.0, 99.0),
                  'input_dbm' => $faker->randomFloat(2, 10, 30),
                  'created_at' => Carbon::now(),
                  'updated_at' => Carbon::now(),
              ]);
              for ($j = 1; $j <= 4; $j++) {
                  SnPorts::create([
                      'name' => 'SN-' . $j,
                      'dn_id' => $dn->id,
                      'description' => 'C45 DN-'.$i.'/SN-'. $j,
                      'location' => $faker->latitude(16.0, 22.0) . ',' . $faker->longitude(94.0, 99.0),
                      'input_dbm' => $faker->randomFloat(2, 10, 30),
                      'created_at' => Carbon::now(),
                      'updated_at' => Carbon::now(),
                  ]);
              }
          }
    }
}
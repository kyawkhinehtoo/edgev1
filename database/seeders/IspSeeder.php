<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Isp;

class IspSeeder extends Seeder
{
    public function run(): void
    {
        $isps = [
            [
                'name' => 'Myanmar Net',
                'short_code' => 'MN',
                'address' => 'Yangon, Myanmar',
                'contact_person' => 'John Doe',
                'phone' => '09123456789',
                'email' => 'contact@myanmarnet.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'True Net',
                'short_code' => 'TN',
                'address' => 'Mandalay, Myanmar',
                'contact_person' => 'Jane Smith',
                'phone' => '09987654321',
                'email' => 'contact@truenet.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Global Net',
                'short_code' => 'GN',
                'address' => 'Naypyidaw, Myanmar',
                'contact_person' => 'Mike Johnson',
                'phone' => '09456789123',
                'email' => 'contact@globalnet.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($isps as $isp) {
            Isp::create($isp);
        }
    }
}

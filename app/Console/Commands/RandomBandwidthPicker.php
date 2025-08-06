<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\InstallationService;
use App\Models\MaintenanceService;
use App\Models\PortSharingService;
use Illuminate\Console\Command;

class RandomBandwidthPicker extends Command
{
    protected $signature = 'bandwidth:pick';
    protected $description = 'Pick a random bandwidth and get matching portsharing ID';

    public function handle()
    {
        $customers = Customer::all();
        $bandwidthOptions = [10, 20, 30, 35, 40, 50, 55, 60, 80, 100, 120, 150, 200];

        foreach ($customers as $customer) {
            // Uncomment this if you only want to update null/zero bandwidth customers
            // if ($customer->bandwidth == 0 || $customer->bandwidth == null) {

            $bandwidth = $customer->bandwidth;

            $portSharingService = PortSharingService::where('max_speed', '>=', $bandwidth)
                ->where('type', strtolower($customer->service_type))
                ->orderBy('max_speed')
                ->first();

         //   $maintenanceService = MaintenanceService::inRandomOrder()->first();
           // $installationService = InstallationService::where('type', 'new')->inRandomOrder()->first();

            if (!$portSharingService ) {
                $this->warn("Skipping {$customer->name} due to missing service data.");
                continue;
            }

            $customer->bandwidth = $bandwidth;
            $customer->port_sharing_service_id = $portSharingService->id;
            // $customer->installation_service_id = $installationService->id;
            // $customer->maintenance_service_id = $maintenanceService->id;

            $customer->update();

            $this->info("Updated Customer: {$customer->name}");
            $this->info("- Bandwidth: {$bandwidth} Mbps");
            // $this->info("- Installation: {$installationService->name}");
            // $this->info("- Maintenance: {$maintenanceService->name}");
            // }
        }
    }
}
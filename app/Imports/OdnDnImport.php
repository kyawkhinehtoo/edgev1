<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\DnPorts;
use App\Models\DnSplitter;
use App\Models\DnBox;
use App\Models\FiberCable;
use App\Models\PopDevice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OdnDnImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['cabinet']) && empty($row['dn']) && empty($row['linked_olt'])) {
            return null;
        }

        try {
            // Find or create DN Box (Cabinet)
            $dnBox = DnBox::firstOrCreate(
                ['name' => $row['cabinet']],
                [
                    'type' => 'cabinet',
                    'status' => 'active',
                    'location' =>  $row['location'] ?? '16.8661,96.1951',
                    'description' => 'Imported DN Box'
                ]
            );

            // Find PopDevice by device_name (Linked OLT)
            $popDevice = PopDevice::where('device_name', $row['linked_olt'])->first();
            if (!$popDevice) {
                Storage::append('odn_dn_import.log', "PopDevice not found: " . $row['linked_olt']);
                return null;
            }

            // Find FiberCable by name (Source Fiber)
            $fiberCable = FiberCable::where('name', $row['source_fiber'])->first();
            if (!$fiberCable) {
                Storage::append('odn_dn_import.log', "FiberCable not found: " . $row['source_fiber']);
                return null;
            }

            // Parse core color and number from "Source Fiber Core Color & Number"
            $coreInfo = $row['source_fiber_core_color_number'];
            $coreNumber = null;
            $coreColor = null;
            
            // Try to extract number and color from the field
            // Assuming format like "Blue-1", "Red-2", "1-Blue", etc.
            if (preg_match('/(\d+)/', $coreInfo, $numberMatches)) {
                $coreNumber = (int)$numberMatches[1];
            }
            
            if (preg_match('/([a-zA-Z]+)/', $coreInfo, $colorMatches)) {
                $coreColor = ucfirst(strtolower($colorMatches[1]));
            }

            // Create or update DN Splitter
            $dnSplitter = DnSplitter::updateOrCreate(
                [
                    'name' => $row['dn'],
                    'dn_id' => $dnBox->id,
                ],
                [
                    'pop_device_id' => $popDevice->id,
                    'fiber_id' => $fiberCable->id,
                    'core_number' => $coreNumber,
                    'core_color' => $coreColor,
                    'location' => $row['location'] ?? '16.8661,96.1951',
                    'status' => strtolower($row['status']) === 'active' ? 'active' : 'plan'
                ]
            );

            Storage::append('odn_dn_import.log', "Successfully imported/updated DN Splitter: " . $row['dn']);
            
            return $dnSplitter;

        } catch (\Exception $e) {
            Storage::append('odn_dn_import.log', "Error importing row: " . $e->getMessage() . " - Row data: " . json_encode($row));
            return null;
        }
    }
}
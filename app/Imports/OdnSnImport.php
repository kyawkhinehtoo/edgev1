<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\SnBox;
use App\Models\SnSplitter;
use App\Models\DnSplitter;
use App\Models\FiberCable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OdnSnImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['sn_name']) && empty($row['dn_splitter'])) {
            return null;
        }

        try {
            // Find DN Splitter by name
            $dnSplitter = DnSplitter::where('name', trim($row['dn_splitter']))->first();
            if (!$dnSplitter) {
                Storage::append('odn_sn_import.log', "DN Splitter not found: " . $row['dn_splitter']);
                return null;
            }

            // Validate location format (lat,lng)
            $location = trim($row['location']);
            if (!preg_match('/^-?\d+(\.\d+)?,-?\d+(\.\d+)?$/', $location)) {
                Storage::append('odn_sn_import.log', "Invalid location format: " . $location . " for SN: " . $row['sn_name']);
                $location = '16.8661,96.1951'; // Default location
            }

            // Validate status
            $validStatuses = ['active', 'inactive'];
            $status = strtolower(trim($row['status'] ?? 'active'));
            if (!in_array($status, $validStatuses)) {
                Storage::append('odn_sn_import.log', "Invalid status: " . $row['status'] . " for SN: " . $row['sn_name']);
                $status = 'active'; // Default to active
            }

            // Create or update SN Box
            $snBox = SnBox::updateOrCreate(
                [
                    'name' => trim($row['sn_name']),
                    'dn_splitter_id' => $dnSplitter->id,
                ],
                [
                    'location' => $location,
                    'status' => $status
                ]
            );

            // If SN Splitter data is provided, create SN Splitter
            if (!empty($row['sn_splitter_name'])) {
                // Validate fiber type
                $validFiberTypes = ['patch_chord', 'distributed_route'];
                $fiberType = strtolower(trim($row['fiber_type'] ?? 'patch_chord'));
                if (!in_array($fiberType, $validFiberTypes)) {
                    Storage::append('odn_sn_import.log', "Invalid fiber_type: " . $row['fiber_type'] . " for SN Splitter: " . $row['sn_splitter_name']);
                    $fiberType = 'patch_chord'; // Default to patch_chord
                }

                // Validate port number
                $portNumber = (int)($row['port_number'] ?? 1);
                if ($portNumber < 1 || $portNumber > 8) {
                    Storage::append('odn_sn_import.log', "Invalid port_number: " . $portNumber . " for SN Splitter: " . $row['sn_splitter_name'] . ". Setting to 1.");
                    $portNumber = 1;
                }

                $snSplitterData = [
                    'name' => trim($row['sn_splitter_name']),
                    'sn_id' => $snBox->id,
                    'fiber_type' => $fiberType,
                    'port_number' => $portNumber,
                    'location' => $location,
                    'status' => $status
                ];

                // If fiber type is distributed_route, add fiber and core info
                if ($fiberType === 'distributed_route') {
                    if (!empty($row['fiber_cable'])) {
                        $fiberCable = FiberCable::where('name', trim($row['fiber_cable']))->first();
                        if ($fiberCable) {
                            $snSplitterData['fiber_id'] = $fiberCable->id;
                        } else {
                            Storage::append('odn_sn_import.log', "Fiber cable not found: " . $row['fiber_cable']);
                        }
                    }

                    if (!empty($row['core_number'])) {
                        $coreNumber = (int)$row['core_number'];
                        if ($coreNumber >= 1) {
                            $snSplitterData['core_number'] = $coreNumber;
                        }
                    }
                }

                // Create or update SN Splitter
                SnSplitter::updateOrCreate(
                    [
                        'name' => trim($row['sn_splitter_name']),
                        'sn_id' => $snBox->id,
                    ],
                    $snSplitterData
                );

                Storage::append('odn_sn_import.log', "Successfully imported/updated SN Splitter: " . $row['sn_splitter_name']);
            }

            Storage::append('odn_sn_import.log', "Successfully imported/updated SN Box: " . $row['sn_name']);
            
            return $snBox;

        } catch (\Exception $e) {
            Storage::append('odn_sn_import.log', "Error importing row: " . $e->getMessage() . " - Row data: " . json_encode($row));
            return null;
        }
    }
}
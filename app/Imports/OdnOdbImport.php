<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Odb;
use App\Models\Odf;
use App\Models\OdbFiberCable;
use App\Models\FiberCable;
use App\Models\PopDevice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OdnOdbImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['odb_name']) && empty($row['odf_name'])) {
            return null;
        }

        try {
            // Find ODF by name
            $odf = Odf::where('name', trim($row['odf_name']))->first();
            if (!$odf) {
                Storage::append('odn_odb_import.log', "ODF not found: " . $row['odf_name']);
                return null;
            }

            // Validate status
            $validStatuses = ['active','plan', 'inactive', 'maintenance'];
            $status = strtolower(trim($row['status'] ?? 'active'));
            if (!in_array($status, $validStatuses)) {
                Storage::append('odn_odb_import.log', "Invalid status: " . $row['status'] . " for ODB: " . $row['odb_name']);
                $status = 'active'; // Default to active
            }

            // Validate total ports
            $totalPorts = (int)($row['total_ports'] ?? 96);
            if ($totalPorts < 1 || $totalPorts > 96) {
                Storage::append('odn_odb_import.log', "Invalid total_ports: " . $totalPorts . " for ODB: " . $row['odb_name'] . ". Setting to 96.");
                $totalPorts = 96;
            }

            // Create or update ODB
            $odb = Odb::updateOrCreate(
                [
                    'name' => trim($row['odb_name']),
                    'odf_id' => $odf->id,
                ],
                [
                    'total_ports' => $totalPorts,
                    'status' => $status
                ]
            );

            // If fiber cable connection data is provided, create ODB Fiber Cable connection
            if (!empty($row['fiber_cable']) && !empty($row['odb_port'])) {
                $fiberCable = FiberCable::where('name', trim($row['fiber_cable']))->first();
                
                if ($fiberCable) {
                    $connectionData = [
                        'odb_id' => $odb->id,
                        'fiber_cable_id' => $fiberCable->id,
                        'odb_port' => (int)$row['odb_port'],
                    ];

                    // Add optional fields
                    if (!empty($row['pop_device'])) {
                        $popDevice = PopDevice::where('device_name', trim($row['pop_device']))->first();
                        if ($popDevice) {
                            $connectionData['pop_device_id'] = $popDevice->id;
                        }
                    }

                    // Parse Feeder Core Color (e.g., "Blue 1" -> color: blue, port: 1)
                    if (!empty($row['feeder_core_color'])) {
                        $coreColorData = $this->parseFeederCoreColor(trim($row['feeder_core_color']));
                        if ($coreColorData['color']) {
                            $connectionData['fiber_cable_color'] = $coreColorData['color'];
                        }
                        if ($coreColorData['port']) {
                            $connectionData['fiber_cable_port'] = $coreColorData['port'];
                        }
                    }

                    if (!empty($row['olt_port'])) {
                        $connectionData['olt_port'] = (int)$row['olt_port'];
                    }

                    if (!empty($row['olt_cable_label'])) {
                        $connectionData['olt_cable_label'] = trim($row['olt_cable_label']);
                    }

                    if (!empty($row['description'])) {
                        $connectionData['description'] = trim($row['description']);
                    }

                    $connectionData['status'] = strtolower(trim($row['connection_status'] ?? 'active'));

                    // Check if connection already exists
                    $existingConnection = OdbFiberCable::where('odb_id', $odb->id)
                        ->where('odb_port', $connectionData['odb_port'])
                        ->first();

                    if ($existingConnection) {
                        $existingConnection->update($connectionData);
                    } else {
                        OdbFiberCable::create($connectionData);
                    }
                } else {
                    Storage::append('odn_odb_import.log', "Fiber cable not found: " . $row['fiber_cable']);
                }
            }

            Storage::append('odn_odb_import.log', "Successfully imported/updated ODB: " . $row['odb_name']);
            
            return $odb;

        } catch (\Exception $e) {
            Storage::append('odn_odb_import.log', "Error importing row: " . $e->getMessage() . " - Row data: " . json_encode($row));
            return null;
        }
    }

    /**
     * Parse Feeder Core Color string like "Blue 1" into color and port
     * 
     * @param string $feederCoreColor
     * @return array
     */
    private function parseFeederCoreColor($feederCoreColor)
    {
        $result = ['color' => null, 'port' => null];
        
        if (empty($feederCoreColor)) {
            return $result;
        }

        // Split by space and get the last part as potential port number
        $parts = explode(' ', $feederCoreColor);
        
        if (count($parts) >= 2) {
            // Get the last part as potential port number
            $lastPart = array_pop($parts);
            
            // Check if last part is a number
            if (is_numeric($lastPart) && (int)$lastPart > 0) {
                $result['port'] = (int)$lastPart;
                
                // Join remaining parts as color name
                $colorName = implode(' ', $parts);
                $result['color'] = strtolower(trim($colorName));
            } else {
                // If last part is not a number, treat whole string as color
                $result['color'] = strtolower(trim($feederCoreColor));
            }
        } else {
            // Single word, treat as color only
            $result['color'] = strtolower(trim($feederCoreColor));
        }

        // Validate color against known fiber cable colors
        $validColors = ['blue', 'orange', 'green', 'brown', 'gray', 'white', 'red', 'black', 'yellow', 'purple', 'pink', 'aqua'];
        if ($result['color'] && !in_array($result['color'], $validColors)) {
            Storage::append('odn_odb_import.log', "Unknown fiber cable color: " . $result['color'] . ". Original value: " . $feederCoreColor);
        }

        return $result;
    }
}
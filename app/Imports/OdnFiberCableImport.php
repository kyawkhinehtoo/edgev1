<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\FiberCable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OdnFiberCableImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['name']) && empty($row['type']) && empty($row['cable_layout'])) {
            return null;
        }

        try {
            // Validate type
            $validTypes = ['feeder', 'sub_feeder', 'distributed_route'];
            $type = strtolower(trim($row['type']));
            if (!in_array($type, $validTypes)) {
                Storage::append('odn_fiber_cable_import.log', "Invalid type: " . $row['type'] . " for cable: " . $row['name']);
                return null;
            }

            // Validate cable layout
            $validLayouts = ['UG', 'OH'];
            $cableLayout = strtoupper(trim($row['cable_layout']));
            if (!in_array($cableLayout, $validLayouts)) {
                Storage::append('odn_fiber_cable_import.log', "Invalid cable layout: " . $row['cable_layout'] . " for cable: " . $row['name']);
                return null;
            }

            // Create or update Fiber Cable
            $fiberCable = FiberCable::updateOrCreate(
                [
                    'name' => trim($row['name']),
                ],
                [
                    'core_quantity' => (int)($row['core_quantity'] ?? 12),
                    'type' => $type,
                    'cable_layout' => $cableLayout,
                    'cable_length' => $row['cable_length'] ? (float)$row['cable_length'] : null,
                    'status' => trim($row['status']) ?: 'Active'
                ]
            );

            Storage::append('odn_fiber_cable_import.log', "Successfully imported/updated Fiber Cable: " . $row['name']);
            
            return $fiberCable;

        } catch (\Exception $e) {
            Storage::append('odn_fiber_cable_import.log', "Error importing row: " . $e->getMessage() . " - Row data: " . json_encode($row));
            return null;
        }
    }
}
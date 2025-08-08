<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\JcBox;
use App\Models\CoreAssignment;
use App\Models\FiberCable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OdnJcImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['jc_name']) && empty($row['location'])) {
            return null;
        }

        try {
            // Validate type
            $validTypes = ['jc', 'cabinet'];
            $type = strtolower(trim($row['type'] ?? 'jc'));
            if (!in_array($type, $validTypes)) {
                Storage::append('odn_jc_import.log', "Invalid type: " . $row['type'] . " for JC: " . $row['jc_name']);
                $type = 'jc'; // Default to jc
            }

            // Validate location format (lat,lng)
            $location = trim($row['location']);
            if (!preg_match('/^-?\d+(\.\d+)?,-?\d+(\.\d+)?$/', $location)) {
                Storage::append('odn_jc_import.log', "Invalid location format: " . $location . " for JC: " . $row['jc_name']);
                $location = '16.8661,96.1951'; // Default location
            }

            // Create or update JC Box
            $jcBox = JcBox::updateOrCreate(
                [
                    'name' => trim($row['jc_name']),
                ],
                [
                    'location' => $location,
                    'type' => $type,
                    'status' => trim($row['status']) ?: 'Active'
                ]
            );

            // If core assignment data is provided, create core assignments
            if (!empty($row['source_fiber']) && !empty($row['dest_fiber'])) {
                $sourceFiber = FiberCable::where('name', trim($row['source_fiber']))->first();
                $destFiber = FiberCable::where('name', trim($row['dest_fiber']))->first();

                if ($sourceFiber && $destFiber) {
                    CoreAssignment::updateOrCreate(
                        [
                            'source_id' => $sourceFiber->id,
                            'dest_id' => $destFiber->id,
                            'jc_id' => $jcBox->id,
                     
                            'source_color' => $row['source_color'] ? strtolower(trim($row['source_color'])) : null,
                            'source_port' => $row['source_port'] ? (int)$row['source_port'] : null,
                            'dest_color' => $row['dest_color'] ? strtolower(trim($row['dest_color'])) : null,
                            'dest_port' => $row['dest_port'] ? (int)$row['dest_port'] : null,
                            'status' => trim($row['assignment_status']) ?: 'active'
                        ]
                    );
                } else {
                    if (!$sourceFiber) {
                        Storage::append('odn_jc_import.log', "Source fiber cable not found: " . $row['source_fiber']);
                    }
                    if (!$destFiber) {
                        Storage::append('odn_jc_import.log', "Destination fiber cable not found: " . $row['dest_fiber']);
                    }
                }
            }

            Storage::append('odn_jc_import.log', "Successfully imported/updated JC Box: " . $row['jc_name']);
            
            return $jcBox;

        } catch (\Exception $e) {
            Storage::append('odn_jc_import.log', "Error importing row: " . $e->getMessage() . " - Row data: " . json_encode($row));
            return null;
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\SnSplitter;
use App\Models\SnBox;
use App\Models\FiberCable;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SnSplitterController extends Controller
{
    public function index()
    {
        $snSplitters = SnSplitter::with(['snBox', 'fiberCable'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return Inertia::render('SnSplitter/Index', [
            'snSplitters' => $snSplitters
        ]);
    }

    public function create()
    {
        $snBoxes = SnBox::get();
        $fiberCables = FiberCable::get();

        return Inertia::render('SnSplitter/Create', [
            'snBoxes' => $snBoxes,
            'fiberCables' => $fiberCables
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sn_id' => 'required|exists:sn_boxes,id',
            'port_number' => 'required|integer|min:1|max:8', // Add validation for 'port_number'
            'fiber_type' => 'required|string|in:patch_chord,distributed_route',
            'fiber_id' => 'nullable|required_if:fiber_type,distributed_route|exists:fiber_cables,id',
            'core_number' => 'nullable|required_if:fiber_type,distributed_route|integer|min:1',
            'location' => [
                'required',
                'string',
                'regex:/^-?([0-8]?[0-9]|90)(\.[0-9]+)?,\s*-?((1[0-7][0-9])|([0-9]?[0-9]))(\.[0-9]+)?$/',
                function ($attribute, $value, $fail) {
                    $coordinates = array_map('trim', explode(',', $value));
                    if (count($coordinates) !== 2) {
                        $fail('Location must be in latitude,longitude format.');
                        return;
                    }
                    
                    $latitude = floatval($coordinates[0]);
                    $longitude = floatval($coordinates[1]);
                    
                    if ($latitude < -90 || $latitude > 90) {
                        $fail('Latitude must be between -90 and 90 degrees.');
                    }
                    
                    if ($longitude < -180 || $longitude > 180) {
                        $fail('Longitude must be between -180 and 180 degrees.');
                    }
                }
            ],
            'status' => 'required|string|in:active,inactive'
        ]);

        SnSplitter::create($validated);

        return redirect()->route('sn-splitters.index')
            ->with('message', 'SN Splitter created successfully.');
    }

    public function edit(SnSplitter $snSplitter)
    {
        $snBoxes = SnBox::get();
        $fiberCables = FiberCable::get();

        return Inertia::render('SnSplitter/Edit', [
            'snSplitter' => $snSplitter,
            'snBoxes' => $snBoxes,
            'fiberCables' => $fiberCables
        ]);
    }

    public function update(Request $request, SnSplitter $snSplitter)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sn_id' => 'required|exists:sn_boxes,id',
            'port_number' => 'required|integer|min:1|max:8', // Add validation for 'port_number'
            'fiber_type' => 'required|string|in:patch_chord,distributed_route',
            'fiber_id' => 'nullable|required_if:fiber_type,distributed_route|exists:fiber_cables,id',
            'core_number' => 'nullable|required_if:fiber_type,distributed_route|integer|min:1',
            'location' => [
                'required',
                'string',
                'regex:/^-?([0-8]?[0-9]|90)(\.[0-9]+)?,\s*-?((1[0-7][0-9])|([0-9]?[0-9]))(\.[0-9]+)?$/',
                function ($attribute, $value, $fail) {
                    $coordinates = array_map('trim', explode(',', $value));
                    if (count($coordinates) !== 2) {
                        $fail('Location must be in latitude,longitude format.');
                        return;
                    }
                    
                    $latitude = floatval($coordinates[0]);
                    $longitude = floatval($coordinates[1]);
                    
                    if ($latitude < -90 || $latitude > 90) {
                        $fail('Latitude must be between -90 and 90 degrees.');
                    }
                    
                    if ($longitude < -180 || $longitude > 180) {
                        $fail('Longitude must be between -180 and 180 degrees.');
                    }
                }
            ],
            'status' => 'required|string|in:active,inactive'
        ]);

        $snSplitter->update($validated);

        return redirect()->route('sn-splitters.index')
            ->with('message', 'SN Splitter updated successfully.');
    }

    public function destroy(SnSplitter $snSplitter)
    {
        $snSplitter->delete();
        
        return redirect()->route('sn-splitters.index')
            ->with('message', 'SN Splitter deleted successfully.');
    }
}
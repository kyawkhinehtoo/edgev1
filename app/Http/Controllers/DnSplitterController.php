<?php

namespace App\Http\Controllers;

use App\Models\DnSplitter;
use App\Models\DnBox;
use App\Models\FiberCable;
use App\Models\Pop;
use App\Models\PopDevice;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DnSplitterController extends Controller
{
    public function index()
    {
        $dnSplitters = DnSplitter::with(['dnBox', 'fiberCable','popDevice'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return Inertia::render('DnSplitter/Index', [
            'dnSplitters' => $dnSplitters
        ]);
    }

    public function create()
    {
        $pops = Pop::all();
        $popDevices = PopDevice::all();
        $dnBoxes = DnBox::where('status', 'active')->get();
        $fiberCables = FiberCable::get();

        return Inertia::render('DnSplitter/Create', [
            'popDevices' => $popDevices,
            'pops' => $pops,
            'dnBoxes' => $dnBoxes,
            'fiberCables' => $fiberCables
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'pop_device_id' => 'required|exists:pop_devices,id',
            'dn_id' => 'required|exists:dn_boxes,id',
            'fiber_id' => 'required|exists:fiber_cables,id',
            'core_number' => 'required|integer|min:1',
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

        DnSplitter::create($validated);

        return redirect()->route('dn-splitters.index')
            ->with('message', 'DN Splitter created successfully.');
    }

    public function edit(DnSplitter $dnSplitter)
    {
        $dnBoxes = DnBox::where('status', 'active')->get();
        $fiberCables = FiberCable::get();
        $pops = Pop::all();
        
        // Load the popDevice relation
        $dnSplitter->load('popDevice', 'popDevice.pop');

        return Inertia::render('DnSplitter/Edit', [
            'dnSplitter' => $dnSplitter,
            'dnBoxes' => $dnBoxes,
            'fiberCables' => $fiberCables,
            'pops' => $pops
        ]);
    }

    public function update(Request $request, DnSplitter $dnSplitter)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'pop_device_id' => 'required|exists:pop_devices,id',
            'dn_id' => 'required|exists:dn_boxes,id',
            'fiber_id' => 'required|exists:fiber_cables,id',
            'core_number' => 'required|integer|min:1',
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

        $dnSplitter->update($validated);

        return redirect()->route('dn-splitters.index')
            ->with('message', 'DN Splitter updated successfully.');
    }

    public function destroy(DnSplitter $dnSplitter)
    {
        $dnSplitter->delete();
        
        return redirect()->route('dn-splitters.index')
            ->with('message', 'DN Splitter deleted successfully.');
    }
}
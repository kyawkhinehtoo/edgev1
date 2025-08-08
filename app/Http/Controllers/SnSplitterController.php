<?php

namespace App\Http\Controllers;

use App\Models\SnSplitter;
use App\Models\SnBox;
use App\Models\FiberCable;
use App\Models\DnBox;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;


class SnSplitterController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->load('role');
        if (!$user->role->dn_panel) {
            abort(403, 'Unauthorized access.');
        }
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
            'fiber_color' => 'nullable|required_if:fiber_type,distributed_route|string|max:50',
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
            'fiber_color' => 'nullable|required_if:fiber_type,distributed_route|string|max:50',
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

    public function nearby(Request $request)
    {
        $user = Auth::user();
        $user->load('role');
        if (!$user->role->check_sn) {
              abort(403, 'Unauthorized access.');
        }
        $type = $request->input('type', 'sn_splitter');
        $location = $request->input('location');
        $radius = $request->input('radius', 10); // default 10km
        $perPage = $request->input('per_page', 10);
        
        if (!$location) {
            return Inertia::render('SnSplitter/Nearby', [
                'splitters' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage),
                'dnBoxes' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage),
                'location' => null,
                'radius' => $radius,
                'type' => $type,
                'error' => 'Location is required.'
            ]);
        }
        
        if ($type == 'sn_splitter') {
            $splitters = \App\Models\SnSplitter::nearby($location, $radius)->paginate($perPage);
            $dnBoxes = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);
        } else {
            $dnBoxes = \App\Models\DnBox::nearby($location, $radius)->paginate($perPage);
            $splitters = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);
        }
        
        return Inertia::render('SnSplitter/Nearby', [
            'splitters' => $splitters,
            'dnBoxes' => $dnBoxes,
            'location' => $location,
            'radius' => $radius,
            'type' => $type,
            'error' => null
        ]);
    }
}
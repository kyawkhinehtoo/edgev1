<?php

namespace App\Http\Controllers;

use App\Models\Township;
use App\Models\Zone;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ZoneController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $zones = Zone::query()
            ->with('townships')  // Add this line to eager load townships
            ->when($search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Zone/Index', [
            'zones' => $zones,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Zone/Create', [
            'townships' => Township::select('id', 'name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'township_ids' => 'required|array',
            'township_ids.*' => 'exists:townships,id'
        ]);
    
        $zone = Zone::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'is_active' => $validated['is_active']
        ]);
    
        $zone->townships()->attach($validated['township_ids']);
    
        return redirect()->route('zone.index')
            ->with('message', 'Zone created successfully');
    }

    public function edit(Zone $zone)
    {
        return Inertia::render('Zone/Edit', [
            'zone' => $zone->load('townships'),
            'townships' => Township::select('id', 'name')->get()
        ]);
    }

    public function update(Request $request, Zone $zone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'township_ids' => 'required|array',
            'township_ids.*' => 'exists:townships,id'
        ]);
    
        $zone->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'is_active' => $validated['is_active']
        ]);
    
        $zone->townships()->sync($validated['township_ids']);
    
        return redirect()->route('zone.index')
            ->with('message', 'Zone updated successfully');
    }

    public function destroy(Zone $zone)
    {
        $zone->delete();

        return redirect()->route('zone.index')
            ->with('message', 'Zone deleted successfully');
    }
}
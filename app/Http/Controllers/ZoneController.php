<?php

namespace App\Http\Controllers;

use App\Models\Township;
use App\Models\User;
use App\Models\Zone;
use App\Models\City;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ZoneController extends Controller
{
    public function __construct(){
        $user = User::with('role')->find(auth()->id());
        if(!$user->role->system_setting){
             abort(403); // Unauthorized access for non-dn_panel users
        }
    }
    public function index(Request $request)
    {
        $search = $request->input('search');
        $city_id = $request->input('city_id');
        
        $zones = Zone::query()
            ->with(['townships', 'city'])
            ->when($search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($city_id, function ($query, $city_id) {
                $query->where('city_id', $city_id);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Zone/Index', [
            'zones' => $zones,
            'cities' => City::select('id', 'name')->orderBy('name')->get(),
            'filters' => $request->only(['search', 'city_id'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Zone/Create', [
            'cities' => City::select('id', 'name')->orderBy('name')->get(),
            'townships' => Township::with('city')->select('id', 'name', 'city_id')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'city_id' => 'required|exists:cities,id',
            'township_ids' => 'required|array',
            'township_ids.*' => 'exists:townships,id'
        ]);
    
        $zone = Zone::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'is_active' => $validated['is_active'],
            'city_id' => $validated['city_id']
        ]);
    
        $zone->townships()->attach($validated['township_ids']);
    
        return redirect()->route('zone.index')
            ->with('message', 'Zone created successfully');
    }

    public function edit(Zone $zone)
    {
        return Inertia::render('Zone/Edit', [
            'zone' => $zone->load('townships'),
            'cities' => City::select('id', 'name')->orderBy('name')->get(),
            'townships' => Township::with('city')->select('id', 'name', 'city_id')->get()
        ]);
    }

    public function update(Request $request, Zone $zone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'city_id' => 'required|exists:cities,id',
            'township_ids' => 'required|array',
            'township_ids.*' => 'exists:townships,id'
        ]);
    
        $zone->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'is_active' => $validated['is_active'],
            'city_id' => $validated['city_id']
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
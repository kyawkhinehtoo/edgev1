<?php

namespace App\Http\Controllers;

use App\Models\DnBox;
use App\Models\Township;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class DnBoxController extends Controller
{
    public function index()
    {
        $dnBoxes = DnBox::with('township')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return Inertia::render('DnBox/Index', [
            'dnBoxes' => $dnBoxes
        ]);
    }

    public function create()
    {
        return Inertia::render('DnBox/Create', [
            'townships' => Township::all(),
        ]);
        
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
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
            'type' => 'required|string|in:dnbox,cabinet',
            'township_id' => 'nullable|exists:townships,id',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|string|in:active,inactive,plan'
        ], [
            'location.regex' => 'Location must be in valid latitude,longitude format (e.g. 16.8661, 96.1951).'
        ]);

        DnBox::create($validated);

        return redirect()->route('dn-boxes.index')
            ->with('message', 'DN Box created successfully.');
    }

    public function edit(DnBox $dnBox)
    {
        return Inertia::render('DnBox/Edit', [
            'dnBox' => $dnBox->load('township'),
            'townships' => Township::all(),
        ]);
    }

    public function update(Request $request, DnBox $dnBox)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
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
            'type' => 'required|string|in:dnbox,cabinet',
            'township_id' => 'nullable|exists:townships,id',
            'description' => 'nullable|string|max:1000',
           'status' => 'required|string|in:active,inactive,plan'
        ], [
            'location.regex' => 'Location must be in valid latitude,longitude format (e.g. 16.8661, 96.1951).'
        ]);

        $dnBox->update($validated);

        return redirect()->route('dn-boxes.index')
            ->with('message', 'DN Box updated successfully.');
    }

    public function destroy(DnBox $dnBox)
    {
        $dnBox->delete();
        
        return redirect()->route('dn-boxes.index')
            ->with('message', 'DN Box deleted successfully.');
    }
}
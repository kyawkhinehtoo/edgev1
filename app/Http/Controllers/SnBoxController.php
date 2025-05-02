<?php

namespace App\Http\Controllers;

use App\Models\SnBox;
use App\Models\DnSplitter;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SnBoxController extends Controller
{
    public function index()
    {
        $snBoxes = SnBox::with('dnSplitter')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return Inertia::render('SnBox/Index', [
            'snBoxes' => $snBoxes
        ]);
    }

    public function create()
    {
        $dnSplitters = DnSplitter::where('status', 'active')->get();

        return Inertia::render('SnBox/Create', [
            'dnSplitters' => $dnSplitters
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dn_splitter_id' => 'required|exists:dn_splitters,id',
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

        SnBox::create($validated);

        return redirect()->route('sn-boxes.index')
            ->with('message', 'SN Box created successfully.');
    }

    public function edit(SnBox $snBox)
    {
        $dnSplitters = DnSplitter::where('status', 'active')->get();

        return Inertia::render('SnBox/Edit', [
            'snBox' => $snBox,
            'dnSplitters' => $dnSplitters
        ]);
    }

    public function update(Request $request, SnBox $snBox)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dn_splitter_id' => 'required|exists:dn_splitters,id',
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

        $snBox->update($validated);

        return redirect()->route('sn-boxes.index')
            ->with('message', 'SN Box updated successfully.');
    }

    public function destroy(SnBox $snBox)
    {
        $snBox->delete();
        
        return redirect()->route('sn-boxes.index')
            ->with('message', 'SN Box deleted successfully.');
    }
}
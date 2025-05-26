<?php

namespace App\Http\Controllers;

use App\Models\RootCause;
use App\Models\SubRootCause;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class SubRootCauseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $user = User::with('role')->find(auth()->id());
        if(!$user->role->system_setting){
             abort(403); // Unauthorized access for non-dn_panel users
        }
    }
    public function index(Request $request)
    {
        $query = SubRootCause::with('rootCause');
        
        // Filter by root_cause_id if provided
        if ($request->has('root_cause_id') && $request->root_cause_id) {
            $query->where('root_cause_id', $request->root_cause_id);
        }
        
        $subRootCauses = $query->orderBy('name')->paginate(10);
        
        // Get all active root causes for the filter
        $rootCauses = RootCause::where('status', 'active')
            ->orderBy('name')
            ->get();
        
        return Inertia::render('SubRootCause/Index', [
            'subRootCauses' => $subRootCauses,
            'rootCauses' => $rootCauses,
            'filters' => $request->only('root_cause_id')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $rootCauses = RootCause::where('status', 'active')
            ->orderBy('name')
            ->get();
            
        return Inertia::render('SubRootCause/Create', [
            'rootCauses' => $rootCauses
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'root_cause_id' => 'required|exists:root_causes,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:active,inactive',
        ]);

        SubRootCause::create($validated);

        return Redirect::route('sub-root-causes.index')
            ->with('message', 'Sub Root Cause created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SubRootCause $subRootCause)
    {
        return Inertia::render('SubRootCause/Show', [
            'subRootCause' => $subRootCause->load('rootCause')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubRootCause $subRootCause)
    {
        $rootCauses = RootCause::where('status', 'active')
            ->orderBy('name')
            ->get();
            
        return Inertia::render('SubRootCause/Edit', [
            'subRootCause' => $subRootCause,
            'rootCauses' => $rootCauses
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubRootCause $subRootCause)
    {
        $validated = $request->validate([
            'root_cause_id' => 'required|exists:root_causes,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:active,inactive',
        ]);

        $subRootCause->update($validated);

        return Redirect::route('sub-root-causes.index')
            ->with('message', 'Sub Root Cause updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubRootCause $subRootCause)
    {
        $subRootCause->delete();

        return Redirect::route('sub-root-causes.index')
            ->with('message', 'Sub Root Cause deleted successfully.');
    }
    
    /**
     * Get sub root causes for a specific root cause.
     */
    public function getByRootCause($rootCauseId)
    {
        $subRootCauses = SubRootCause::where('root_cause_id', $rootCauseId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
            
        return response()->json($subRootCauses);
    }
}
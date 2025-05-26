<?php

namespace App\Http\Controllers;

use App\Models\RootCause;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class RootCauseController extends Controller
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
        $query = RootCause::with('subRootCauses');
        
        // Filter by search term
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Filter by installation
        if ($request->has('is_installation') && trim($request->is_installation) !== '') {
            $isInstallation = $request->is_installation === 'true';
            $query->where('is_installation', $isInstallation);
        }
        
        // Filter by maintenance
        if ($request->has('is_maintenance') && trim($request->is_maintenance) !== '') {
            $isMaintenance = $request->is_maintenance === 'true';
            $query->where('is_maintenance', $isMaintenance);
        }
        
        // Filter by pending_reason
        if ($request->has('is_pending') && trim($request->is_pending) !== '') {
            $isPending = $request->is_pending === 'true';
            $query->where('is_pending', $isPending);
        }
        
        $rootCauses = $query->orderBy('name')->paginate(5);
        
        // Append query parameters to pagination links
        $rootCauses->appends([
            'search' => $request->search,
            'is_installation' => $request->is_installation,
            'is_maintenance' => $request->is_maintenance,
            'is_pending' => $request->is_pending
        ]);
        
        return Inertia::render('RootCause/Index', [
            'rootCauses' => $rootCauses,
            'filters' => [
                'search' => $request->search,
                'is_installation' => $request->is_installation,
                'is_maintenance' => $request->is_maintenance,
                'is_pending' => $request->is_pending
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('RootCause/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:active,inactive',
            'is_installation' => 'required|boolean',
            'is_maintenance' => 'required|boolean',
            'is_pending' => 'required|boolean',
        ]);

        RootCause::create($validated);

        return Redirect::route('root-causes.index')
            ->with('message', 'Root Cause created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RootCause $rootCause)
    {
        return Inertia::render('RootCause/Show', [
            'rootCause' => $rootCause->load('subRootCauses')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RootCause $rootCause)
    {
        return Inertia::render('RootCause/Edit', [
            'rootCause' => $rootCause
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RootCause $rootCause)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:active,inactive',
            'is_installation' => 'required|boolean',
            'is_maintenance' => 'required|boolean',
            'is_pending' => 'required|boolean',
        ]);

        $rootCause->update($validated);

        return Redirect::route('root-causes.index')
            ->with('message', 'Root Cause updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RootCause $rootCause)
    {
        $rootCause->delete();

        return Redirect::route('root-causes.index')
            ->with('message', 'Root Cause deleted successfully.');
    }
}
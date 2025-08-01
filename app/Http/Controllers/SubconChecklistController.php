<?php

namespace App\Http\Controllers;

use App\Models\SubconChecklist;
use App\Models\SubconChecklistsGroup;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class SubconChecklistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $user = User::with('role')->find(auth()->id());
        if(!$user->role?->system_setting){
             abort(403); // Unauthorized access for non-dn_panel users
        }
    }
    public function index()
    {
        $checklists = SubconChecklist::with('group')->orderBy('name')->get();
        
        return Inertia::render('SubconChecklist/Index', [
            'checklists' => $checklists
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = SubconChecklistsGroup::orderBy('name')->get();
        return Inertia::render('SubconChecklist/Create', [
            'groups' => $groups
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'has_attachment' => 'boolean',
            'service_type' => 'required|in:installation,maintenance,termination',
            'remarks' => 'required|string',
            'status' => 'required|string|max:255',
            'group_id' => 'required|exists:subcon_checklists_group,id',
        ]);

        SubconChecklist::create($validated);

        return Redirect::route('subcon-checklists.index')
            ->with('message', 'Checklist created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SubconChecklist $subconChecklist)
    {
        return Inertia::render('SubconChecklist/Show', [
            'checklist' => $subconChecklist
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubconChecklist $subconChecklist)
    {
        $groups = SubconChecklistsGroup::orderBy('name')->get();
        return Inertia::render('SubconChecklist/Edit', [
            'checklist' => $subconChecklist,
            'groups' => $groups
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubconChecklist $subconChecklist)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'has_attachment' => 'boolean',
            'service_type' => 'required|in:installation,maintenance,termination',
            'remarks' => 'required|string',
            'status' => 'required|string|max:255',
            'group_id' => 'required|exists:subcon_checklists_group,id',
        ]);

        $subconChecklist->update($validated);

        return Redirect::route('subcon-checklists.index')
            ->with('message', 'Checklist updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubconChecklist $subconChecklist)
    {
        $subconChecklist->delete();

        return Redirect::route('subcon-checklists.index')
            ->with('message', 'Checklist deleted successfully.');
    }

   
}
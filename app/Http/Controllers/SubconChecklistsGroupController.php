<?php

namespace App\Http\Controllers;

use App\Models\SubconChecklistsGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubconChecklistsGroupController extends Controller
{
      public function __construct(){
        $user = User::with('role')->find(auth()->id());
        if(!$user->role->system_setting){
             abort(403); // Unauthorized access for non-dn_panel users
        }
    }
  
    public function index(Request $request)
    {
        $search = $request->input('group');
        $query = SubconChecklistsGroup::query();
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }
        $groups = $query->orderBy('created_at', 'desc')->paginate(5)->appends(['group' => $search]);
        return Inertia::render('SubconChecklistsGroup/Index', [
            'groups' => $groups,
            'search' => $search,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'required' => 'boolean',
        ]);
        SubconChecklistsGroup::create($validated);
        return redirect()->back()->with('message', 'Group created successfully');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'required' => 'boolean',
        ]);

        $group = SubconChecklistsGroup::findOrFail($request->input('id'));
        $group->update($validated);
        return redirect()->back()->with('message', 'Group updated successfully');
    }

    public function destroy(SubconChecklistsGroup $group)
    {
        $group->delete();
        return redirect()->back()->with('message', 'Group deleted successfully');
    }
}

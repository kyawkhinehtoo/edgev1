<?php

namespace App\Http\Controllers;

use App\Models\FiberCable;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FiberCableController extends Controller
{
    public function index(Request $request)
    {
        $query = FiberCable::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', 'like', '%' . $request->status . '%');
        }

        if ($request->filled('cable_layout')) {
            $query->where('cable_layout', $request->cable_layout);
        }

        $fiberCables = $query->orderBy('created_at', 'desc')->paginate(15);

        return Inertia::render('FiberCable/Index', [
            'fiberCables' => $fiberCables,
            'filters' => $request->only(['name', 'type', 'status', 'cable_layout'])
        ]);
    }

    public function create()
    {
        return Inertia::render('FiberCable/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'core_quantity' => 'required|integer',
            'cable_length' => 'nullable|numeric',
            'type' => 'required|in:feeder,sub_feeder,distributed_route',
            'cable_layout' => 'required|in:UG,OH',
            'status' => 'required|string|max:255'
        ]);

        FiberCable::create($validated);

        return redirect()->route('fiber-cables.index')
            ->with('message', 'Fiber Cable created successfully.');
    }

    public function edit(FiberCable $fiberCable)
    {
        return Inertia::render('FiberCable/Edit', [
            'fiberCable' => $fiberCable
        ]);
    }

    public function update(Request $request, FiberCable $fiberCable)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'core_quantity' => 'required|integer',
            'cable_length' => 'nullable|numeric',
            'type' => 'required|in:feeder,sub_feeder,distributed_route',
            'cable_layout' => 'required|in:UG,OH',
            'status' => 'required|string|max:255'
        ]);

        $fiberCable->update($validated);

        return redirect()->route('fiber-cables.index')
            ->with('message', 'Fiber Cable updated successfully.');
    }

    public function destroy(FiberCable $fiberCable)
    {
        $fiberCable->delete();
        return redirect()->route('fiber-cables.index')
            ->with('message', 'Fiber Cable deleted successfully.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\CoreAssignment;
use App\Models\FiberCable;
use App\Models\JcBox;
use App\Models\OdbFiberCable;
use App\Models\Pop;
use App\Models\PopDevice;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class CoreAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = CoreAssignment::with(['sourceFiberCable', 'destinationFiberCable', 'jcBox']);

        if ($request->filled('source_id')) {
            $query->where('source_id', $request->source_id);
        }

        if ($request->filled('dest_id')) {
            $query->where('dest_id', $request->dest_id);
        }

        if ($request->filled('jc_id')) {
            $query->where('jc_id', $request->jc_id);
        }

        $coreAssignments = $query->orderBy('created_at', 'desc')->paginate(10);

        return Inertia::render('CoreAssignment/Index', [
            'coreAssignments' => $coreAssignments,
            'filters' => $request->only(['source_id', 'dest_id', 'jc_id']),
            'fiberCables' => FiberCable::select('id', 'name')->get(),
            'jcBoxes' => JcBox::select('id', 'name')->get()
        ]);
    }

    public function create()
    {
        $usedPorts = CoreAssignment::select('source_id', 'source_port', 'dest_id', 'dest_port')->get()
            ->groupBy(['source_id', 'dest_id'])
            ->map(function ($assignments) {
                return [
                    'source_ports' => $assignments->pluck('source_port')->toArray(),
                    'dest_ports' => $assignments->pluck('dest_port')->toArray()
                ];
            });
        $pops = Pop::select('id', 'site_name')->get();
        return Inertia::render('CoreAssignment/Create', [
            'fiberCables' => FiberCable::select('id', 'name')->get(),
            'jcBoxes' => JcBox::select('id', 'name')->get(),
            'usedPorts' => $usedPorts,
            'pops'=>$pops
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'source_id' => [
                'required',
                'exists:fiber_cables,id',
                Rule::unique('core_assignments')->where(function ($query) use ($request) {
                             return $query->where('source_id', $request->source_id)
                                ->where('jc_id', $request->jc_id)
                                ->where('dest_id', $request->dest_id)
                                ->where('source_port', $request->source_port_id)
                                ->where('dest_port', $request->dest_port_id)
                                ->where('status', $request->status_id)
                                ->where('dest_id', $request->dest_id)
                                ->where('source_color', $request->source_color_id)
                                ->where('dest_color', $request->dest_color_id);
                })
            ],
            'source_color_id' => 'required|string|max:255',
            'source_port_id' => 'required|string|max:255',
            'dest_id' => [
                'required',
                'exists:fiber_cables,id',
            ],
            'dest_color_id' => 'required|string|max:255',
            'dest_port_id' => 'required|string|max:255',
            'jc_id' => 'required|exists:jc_boxes,id',
            'status_id' => 'required|string|max:255'
        ], [
            'source_id.unique' => 'This source and destination cable combination already exists.',
           
        ]);

        $data = [
            'dest_id' => $validated['dest_id'],
            'source_id' => $validated['source_id'],
            'jc_id' => $validated['jc_id'],
            'source_color' => $validated['source_color_id'],
            'source_port' => $validated['source_port_id'],
            'dest_color' => $validated['dest_color_id'],
            'dest_port' => $validated['dest_port_id'],
            'status' => $validated['status_id'],
        ];

        CoreAssignment::create($data);

        return redirect()->route('core-assignments.index')
            ->with('message', 'Core Assignment created successfully.');
    }

    public function edit(CoreAssignment $coreAssignment)
    {
        // Load relationships and get pop device info in a single query
        $coreAssignment = $coreAssignment->load([
            'sourceFiberCable', 
            'destinationFiberCable'
        ]);

        // Get pop device details with a single efficient query
        $popDetails = OdbFiberCable::where('fiber_cable_id', $coreAssignment->sourceFiberCable?->id)
            ->join('pop_devices', 'pop_devices.id', '=', 'odb_fiber_cables.pop_device_id')
            ->select('pop_devices.id as pop_device_id', 'pop_devices.*', 'pop_devices.pop_id')
            ->first();

        return Inertia::render('CoreAssignment/Edit', [
            'coreAssignment' => $coreAssignment,
            'fiberCables' => FiberCable::select('id', 'name')->get(),
            'jcBoxes' => JcBox::select('id', 'name')->get(),
            'pops' => Pop::select('id', 'site_name')->get(),
            'pop_id' => $popDetails?->pop_id,
            'pop_device' => $popDetails
        ]);
    }
    public function update(Request $request, CoreAssignment $coreAssignment)
    {
        $validated = $request->validate([
            'source_id' => [
                'required',
                'exists:fiber_cables,id',
                Rule::unique('core_assignments')->where(function ($query) use ($request) {
                    return $query->where('source_id', $request->source_id)
                                ->where('jc_id', $request->jc_id)
                                ->where('dest_id', $request->dest_id)
                                ->where('source_port', $request->source_port_id)
                                ->where('dest_port', $request->dest_port_id)
                                ->where('status', $request->status_id)
                                ->where('dest_id', $request->dest_id)
                                ->where('source_color', $request->source_color_id)
                                ->where('dest_color', $request->dest_color_id);
                })->ignore($coreAssignment->id)
            ],
            'source_color_id' => 'required|string|max:255',
            'source_port_id' => 'required|max:255',
            'dest_id' => [
                'required',
                'exists:fiber_cables,id',
                'different:source_id'
            ],
            'dest_color_id' => 'required|string|max:255',
            'dest_port_id' => 'required|max:255',
            'jc_id' => 'required|exists:jc_boxes,id',
            'status_id' => 'required|string|max:255'
        ], [
            'source_id.unique' => 'This source and destination cable combination already exists.',
            'dest_id.different' => 'Source and destination cables cannot be the same.'
        ]);

        $data = [
            'dest_id' => $validated['dest_id'],
            'source_id' => $validated['source_id'],
            'jc_id' => $validated['jc_id'],
            'source_color' => $validated['source_color_id'],
            'source_port' => $validated['source_port_id'],
            'dest_color' => $validated['dest_color_id'],
            'dest_port' => $validated['dest_port_id'],
            'status' => $validated['status_id'],
        ];

        $coreAssignment->update($data);

        return redirect()->route('core-assignments.index')
            ->with('message', 'Core Assignment updated successfully!');
    }

    public function destroy(CoreAssignment $coreAssignment)
    {
        $coreAssignment->delete();
        return redirect()->route('core-assignments.index')
            ->with('message', 'Core Assignment deleted successfully.');
    }
}
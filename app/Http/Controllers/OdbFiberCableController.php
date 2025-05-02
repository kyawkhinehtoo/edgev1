<?php

namespace App\Http\Controllers;

use App\Models\Odf;
use App\Models\FiberCable;
use App\Models\Odb;
use App\Models\OdbFiberCable;
use App\Models\PopDevice;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OdbFiberCableController extends Controller
{
    public function index(Request $request)
    {
        $query = OdbFiberCable::with(['odb','odb.odf', 'fiberCable', 'popDevice']);

        if ($request->has('odf')) {
            $query->whereHas('odb.odf', function($q) use ($request) {
                $q->where('id', $request->odf);
            });
        }

        return Inertia::render('OdbFiberCable/Index', [
            'odbFiberCables' => $query->paginate(10),
            'odfs' => Odf::all(),
            'odbs' => Odb::all(),
            'fiberCables' => FiberCable::all()
        ]);
    }

    public function create()
    {
        return Inertia::render('OdbFiberCable/Create', [
            'odfs' => Odf::all(),
            'odbs' => Odb::all(),
            'popDevices' => PopDevice::all(),
            'fiberCables' => FiberCable::where('type','feeder')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'odb_id' => 'required|exists:odbs,id',
            'fiber_cable_id' => 'required|exists:fiber_cables,id',
            'pop_device_id' => 'nullable|exists:pop_devices,id',
            'odb_port' => 'required|integer|unique:odb_fiber_cables,odb_port,NULL,id,odb_id,' . $request->odb_id,
            'olt_port' => 'nullable|integer|unique:odb_fiber_cables,olt_port,NULL,id,pop_device_id,' . $request->pop_device_id,
            'olt_cable_label' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,maintenance'
        ]);

        OdbFiberCable::create($validated);

        return redirect()->route('odb-fiber-cables.index')
            ->with('message', 'ODB Fiber Cable connection created successfully');
    }

    public function edit(OdbFiberCable $OdbFiberCable)
    {
        return Inertia::render('OdbFiberCable/Edit', [
            'odbFiberCable' => $OdbFiberCable->load(['odb.odf', 'odb','fiberCable']),
            'odbs' => Odb::all(),
            'odfs' => Odf::all(),
            'popDevices' => PopDevice::all(),
            'fiberCables' => FiberCable::where('type','feeder')->get(),
        ]);
    }

    public function update(Request $request, OdbFiberCable $OdbFiberCable)
    {
        $validated = $request->validate([
    
            'odb_id' => 'required|exists:odbs,id',
            'fiber_cable_id' => 'required|exists:fiber_cables,id',
            'pop_device_id' => 'nullable|exists:pop_devices,id',
            'odb_port' => 'required|integer|unique:odb_fiber_cables,odb_port,' . $OdbFiberCable->id . ',id,odb_id,' . $request->odb_id,
            'olt_port' => 'nullable|integer|unique:odb_fiber_cables,olt_port,' . $OdbFiberCable->id . ',id,pop_device_id,' . $request->pop_device_id,
            'olt_cable_label' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,maintenance'
        ]);
        
        $OdbFiberCable->update($validated);

        return redirect()->route('odb-fiber-cables.index')
            ->with('message', 'ODF Fiber Cable connection updated successfully');
    }

    public function destroy(OdbFiberCable $OdbFiberCable)
    {
        $OdbFiberCable->delete();

        return redirect()->route('odb-fiber-cables.index')
            ->with('message', 'ODF Fiber Cable connection deleted successfully');
    }
}
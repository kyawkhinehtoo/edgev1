<?php

namespace App\Http\Controllers;

use App\Models\Odb;
use App\Models\Odf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OdbController extends Controller
{
    public function index(Request $request)
    {   
        $query = Odb::with(['odf']);
        if ($request->has('odf')) {
            $query->where('odf_id', $request->odf);
        }
        return Inertia::render('Odb/Index', [
            'odbs' => $query->paginate(10),
            'odfs' => Odf::all()
        ]);
    }

    public function create()
    {
        return Inertia::render('Odb/Create', [
            'odfs' => Odf::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'odf_id' => 'required|exists:odfs,id',
            'name' => 'required|string|unique:odbs,name,NULL,id,odf_id,' . $request->odf_id,
            'total_ports' => 'required|integer|min:1|max:96',
            'status' => 'required|in:active,inactive,maintenance'
        ]);

        Odb::create($validated);

        return redirect()->route('odbs.index')
            ->with('message', 'ODB created successfully');
    }

    public function edit(Odb $odb)
    {
        return Inertia::render('Odb/Edit', [
            'odb' => $odb->load('odf'),
            'odfs' => Odf::all()
        ]);
    }

    public function update(Request $request, Odb $odb)
    {
        $validated = $request->validate([
            'odf_id' => 'required|exists:odfs,id',
            'name' => 'required|string|unique:odbs,name,' . $odb->id . ',id,odf_id,' . $request->odf_id,
            'total_ports' => 'required|integer|min:1|max:96',
            'status' => 'required|in:active,inactive,maintenance'
        ]);

        $odb->update($validated);

        return redirect()->route('odbs.index')
            ->with('message', 'ODB updated successfully');
    }

    public function destroy(Odb $odb)
    {
        $odb->delete();

        return redirect()->route('odbs.index')
            ->with('message', 'ODB deleted successfully');
    }
}
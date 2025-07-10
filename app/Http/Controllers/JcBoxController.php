<?php

namespace App\Http\Controllers;

use App\Models\JcBox;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JcBoxController extends Controller
{
    public function index(Request $request)
    {
        $query = JcBox::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('type')) {
            $query->where('type', 'like', '%' . $request->type . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', 'like', '%' . $request->status . '%');
        }

        $jcBoxes = $query->orderBy('created_at', 'desc')->paginate(10);

        return Inertia::render('JcBox/Index', [
            'jcBoxes' => $jcBoxes,
            'filters' => $request->only(['name', 'location', 'status'])
        ]);
    }

    public function create()
    {
        return Inertia::render('JcBox/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:jc,cabinet', // Add 'type' validation rule
            'location' => ['required', 'string', 'max:255', 'regex:/^-?\d+(\.\d+)?,-?\d+(\.\d+)?$/'],
            'status' => 'required|string|max:255'
        ]);

        JcBox::create($validated);

        return redirect()->route('jc-boxes.index')
            ->with('message', 'JC Box created successfully.');
    }

    public function edit(JcBox $jcBox)
    {
        return Inertia::render('JcBox/Edit', [
            'jcBox' => $jcBox
        ]);
    }

    public function update(Request $request, JcBox $jcBox)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:jc,cabinet',
            'location' => ['required', 'string', 'max:255', 'regex:/^-?\d+(\.\d+)?,-?\d+(\.\d+)?$/'],
            'status' => 'required|string|max:255'
        ]);

        $jcBox->update($validated);

        return redirect()->route('jc-boxes.index')
            ->with('message', 'JC Box updated successfully.');
    }

    public function destroy(JcBox $jcBox)
    {
        $jcBox->delete();
        return redirect()->route('jc-boxes.index')
            ->with('message', 'JC Box deleted successfully.');
    }
}
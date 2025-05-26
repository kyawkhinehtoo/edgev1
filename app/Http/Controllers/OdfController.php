<?php

namespace App\Http\Controllers;

use App\Models\Odf;
use App\Models\Pop;
use App\Models\PopDevice;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OdfController extends Controller
{
    public function __construct(){
        $user = User::with('role')->find(auth()->id());
        if(!$user->role->dn_panel){
             abort(403); // Unauthorized access for non-dn_panel users
        }
    }
    public function index()
    {
       $popDevices = PopDevice::with('pop')->get();
       $odfs = Odf::orderBy('created_at', 'desc')->paginate(10);
       
       return Inertia::render('Odf/Index', [
           'odfs' => $odfs,
           'popDevices' => $popDevices
       ]);
    }

    public function create()
    {
        $popDevices = PopDevice::get();
        $pops =  Pop::get();
        return Inertia::render('Odf/Create', [
            'popDevices' => $popDevices,
            'pops' => $pops
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pop_device_id' => 'required|array',
            'pop_device_id.*' => 'exists:pop_devices,id',
            'name' => 'required|string|min:1|max:255',
            'location' => ['required', 'string', 'max:255', 'regex:/^-?\d+(\.\d+)?,-?\d+(\.\d+)?$/'],
            'description' => 'nullable|string'
        ]);

        Odf::create($validated);

        return redirect()->route('odfs.index')
            ->with('message', 'ODF created successfully.');
    }

    public function update(Request $request, Odf $odf)
    {
        $validated = $request->validate([
            'pop_device_id' => 'required|array',
            'pop_device_id.*' => 'exists:pop_devices,id',
            'name' => 'required|string|min:1|max:255',
            'location' => ['required', 'string', 'max:255', 'regex:/^-?\d+(\.\d+)?,-?\d+(\.\d+)?$/'],
            'description' => 'nullable|string'
        ]);
    
        $odf->update($validated);
    
        // Sync the pop devices
    
    
        return redirect()->route('odfs.index')
            ->with('message', 'ODF updated successfully');
    }

    public function edit(Odf $odf)
    {
        $pops = Pop::get();
        $popDevices = PopDevice::get();

        return Inertia::render('Odf/Edit', [
            'odf' => $odf,
            'pops' => $pops,
            'popDevices' => $popDevices
        ]);
    }

  

    public function destroy(Odf $odf)
    {
        $odf->delete();
        
        return redirect()->route('odfs.index')
            ->with('message', 'ODF deleted successfully.');
    }
}
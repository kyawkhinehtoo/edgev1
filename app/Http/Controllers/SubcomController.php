<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Isp;
use App\Models\Subcom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use App\Models\Township;

class SubcomController extends Controller
{
    public function index()
    {
        return Inertia::render('Setup/Subcom', [
            'subcoms' => Subcom::withCount('users')
                ->with(['installationTownships', 'maintenanceTownships','isps'])
                ->paginate(10),
            'townships' => Township::select('id', 'name')->get(),
            'isps' => Isp::select('id', 'name')->get(),
        ]);
    }

    public function show(Subcom $subcom)
    {
        $subcom->load(['users' => function ($query) {
            $query->select('id', 'name', 'email', 'phone', 'disabled', 'subcom_id', 'last_login_ip', 'last_login_time');
        }]);

        return Inertia::render('Setup/SubcomShow', [
            'subcom' => $subcom
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'disabled' => 'boolean',
            'installation' => 'boolean',
            'maintenance' => 'boolean',
            'maintenance_fees' => 'required|integer',
            'type' => 'nullable|string|max:255',
            'rate' => 'nullable',
            'installation_townships' => 'array',
            'maintenance_townships' => 'array',
        ]);
        if($validated){
            $subcom = new Subcom();
            $subcom->name = $request->name;
            $subcom->contact_person = $request->contact_person;
            $subcom->email = $request->email;
            $subcom->phone = $request->phone;
            $subcom->disabled = $request->disabled;
            $subcom->installation = $request->installation;
            $subcom->maintenance = $request->maintenance;
            $subcom->maintenance_fees = $request->maintenance_fees;
            $subcom->type = $request->type;
            $subcom->rate = json_encode($request->rate);
            $subcom->save();
        }
       

        if ($request->installation_townships) {
            $subcom->installationTownships()->attach(collect($request->installation_townships)->pluck('id'));
        }

        if ($request->maintenance_townships) {
            $subcom->maintenanceTownships()->attach(collect($request->maintenance_townships)->pluck('id'));
        }
        if ($request->isps) {
            $subcom->isp()->attach(collect($request->isps)->pluck('id'));
        }

        return redirect()->back()->with('message', 'Subcom created successfully');
    }

    public function update(Request $request, Subcom $subcom)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'disabled' => 'boolean',
            'installation' => 'boolean',
            'maintenance' => 'boolean',
            'maintenance_fees' => 'required|integer',
            'type' => 'nullable|string|max:255',
            'rate' => 'nullable',
            'installation_townships' => 'array',
            'maintenance_townships' => 'array',
        ]);

        $subcom->update($validated);

        $subcom->installationTownships()->sync(collect($request->installation_townships)->pluck('id'));
        $subcom->maintenanceTownships()->sync(collect($request->maintenance_townships)->pluck('id'));
        $subcom->isps()->sync(collect($request->isps)->pluck('id'));

        return redirect()->back()->with('message', 'Subcom updated successfully');
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            $customer = Customer::where('subcom_id', $request->id)->first();
            if ($customer)
                return redirect()->back()->with('message', 'Subcom cannot delete due to foreign key constraint in Customer Database.');
            Subcom::find($request->input('id'))->delete();
            return redirect()->back()->with('message', 'Subcom deleted successfully.');
        }
    }
}

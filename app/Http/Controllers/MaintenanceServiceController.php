<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenanceService;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class MaintenanceServiceController extends Controller
{
    public function __construct()
    {
        $user = User::with('role')->find(auth()->id());
       // dd($user->user_type );
        if(!$user->role?->system_setting ){
            if( $user->user_type != 'isp' && $user->user_type != 'internal'){
                abort(403); // Unauthorized access for non-isp users
            }
    
        }
    }

    public function index(Request $request)
    {
        $services = MaintenanceService::when($request->service, function ($query, $service) {
            $query->where('name', 'LIKE', '%' . $service . '%');
        })
        ->orderBy('name', 'desc')
        ->paginate(10);

        return Inertia::render('Setup/MaintenanceService', ['services' => $services]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'service_type' => 'required|in:ftth,dia,dplc,ipvpn',
            'sla_hours' => 'required|numeric',
            'fees' => 'required|numeric',
            'currency' => 'required|in:baht,usd,mmk',
            'short_code' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $service = new MaintenanceService();
        $service->name = $request->name;
        $service->sla_hours = $request->sla_hours;
        $service->service_type = $request->service_type;
        $service->fees = $request->fees;
        $service->currency = $request->currency;
        $service->short_code = $request->short_code;
        $service->status = $request->status ? 1 : 0;
        $service->save();

        return redirect()->back()->with('message', 'Maintenance Service created successfully.');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'service_type' => 'required|in:ftth,dia,dplc,ipvpn',
            'sla_hours' => 'required|numeric',
            'fees' => 'required|numeric',
            'currency' => 'required|in:baht,usd,mmk',
            'short_code' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $service = MaintenanceService::findOrFail($id);
        $service->name = $request->name;
        $service->sla_hours = $request->sla_hours;
        $service->service_type = $request->service_type;
        $service->fees = $request->fees;
        $service->currency = $request->currency;
        $service->short_code = $request->short_code;
        $service->status = $request->status ? 1 : 0;
        $service->save();

        return redirect()->back()->with('message', 'Maintenance Service updated successfully.');
    }

    public function destroy($id)
    {
        $service = MaintenanceService::findOrFail($id);
        $service->delete();

        return redirect()->back()->with('message', 'Maintenance Service deleted successfully.');
    }
}
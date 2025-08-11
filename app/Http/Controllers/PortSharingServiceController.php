<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PortSharingService;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class PortSharingServiceController extends Controller
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
        $services = PortSharingService::when($request->service, function ($query, $service) {
            $query->where('name', 'LIKE', '%' . $service . '%');
        })
        ->orderBy('name', 'desc')
        ->paginate(10);

        return Inertia::render('Setup/PortSharingService', ['services' => $services]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'max_speed' => 'required|numeric',
            'type' => 'required|string',
            'rate ' =>'nullable',
            'currency' => 'required|in:baht,usd,mmk',
            'short_code' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $service = new PortSharingService();
        $service->name = $request->name;
        $service->max_speed = $request->max_speed;
        $service->type = $request->type;
        $service->rate = json_encode($request->rate);
        $service->currency = $request->currency;
        $service->short_code = $request->short_code;
        $service->status = $request->status ? 1 : 0;
        $service->save();

        return redirect()->back()->with('message', 'Port Sharing Service created successfully.');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'max_speed' => 'required|numeric',
            'type' => 'required|string',
            'rate ' =>'nullable',
            'currency' => 'required|in:baht,usd,mmk',
            'short_code' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $service = PortSharingService::findOrFail($id);
        $service->name = $request->name;
        $service->max_speed = $request->max_speed;
        $service->type = $request->type;
        $service->rate = json_encode($request->rate);
        $service->currency = $request->currency;
        $service->short_code = $request->short_code;
        $service->status = $request->status ? 1 : 0;
        $service->save();

        return redirect()->back()->with('message',  'Port Sharing Service updated successfully.');
    }

    public function destroy($id)
    {
        $service = PortSharingService::findOrFail($id);
        $service->delete();

        return redirect()->back()->with('message',  'Port Sharing Service deleted successfully.');
    }
}
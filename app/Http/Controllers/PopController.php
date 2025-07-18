<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Partner;
use Illuminate\Http\Request;
use App\Models\Pop;
use App\Models\PopDevice;
use App\Models\Township;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PopController extends Controller
{
    //
    // public function __construct(){
    //     $user = User::with('role')->find(auth()->id());
    //     if(!$user->role->dn_panel || !$user->role->customer_panel){
    //          abort(403); // Unauthorized access for non-dn_panel users
    //     }
    // }
    public function index(Request $request)
    {
        $pops = Pop::with(['townships','partner'])
            ->when($request->keyword, function ($query, $keyword) {
                $query->where('site_name', 'LIKE', '%' . $keyword . '%');
                $query->orwhere('site_description', 'LIKE', '%' . $keyword . '%');
            })
            ->paginate(10);
        $pop_devices = PopDevice::get();
        $pops->appends($request->all())->links();
        $townships = Township::select('id', 'name')->get();
        $partners = Partner::select('id', 'name')->get();
        return Inertia::render(
            'Setup/Pop',
            ['pops' => $pops, 'pop_devices' => $pop_devices, 'townships' => $townships, 'partners' => $partners, 'keyword' => $request->keyword ?? '']
        );
    }
    public function getIdByName(Request $request)
    {
        $sn = null;
        if ($request->name) {

            $sn = DB::table('sn_ports')
                ->join('dn_ports', 'sn_ports.dn_id', '=', 'dn_ports.id')
                ->where('dn_ports.name', '=', $request->name)
                ->select('sn_ports.id as id', 'sn_ports.name as name', 'sn_ports.port as port', 'sn_ports.description as description', 'dn_ports.name as dn_name')
                ->get();
        }
        return response()
            ->json($sn, 200);
    }
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string',
            'site_location' => 'required|string',
            'partner_id' => 'required|exists:partners,id',
            'townships' => 'required|array',
        ])->validate();


        $check_dup = Pop::where('site_name', $request->name)
            ->exists();
        if ($check_dup) {
            return redirect()->back()->withErrors('Pops Name Already Exist!');
        } else {
            $pop = new Pop();
            $pop->site_name = $request->site_name;
            $pop->site_description = $request->site_description;
            $pop->site_location = $request->site_location;
            $pop->partner_id = $request->partner_id;
            $pop->save();
            if ($pop->id && $request->devices) {
                foreach ($request->devices as $devices) {
                    $pop_device = new PopDevice();
                    $pop_device->pop_id = $pop->id;
                    $pop_device->device_name = $devices['device_name'];
                    $pop_device->qty = $devices['qty'];
                    $pop_device->remark =  $devices['remark'] ?? null;
                    $pop_device->save();
                }
            }
            $pop->townships()->attach(collect($request->townships)->pluck('id'));
            return redirect()->back()->with('message', 'POP Site Created Successfully.');
        }
    }
    // public function update(Request $request, $id)
    // {
    //     Validator::make($request->all(), [
    //         'site_name' => ['required'],
    //     ])->validate();

    //     if ($request->has('id')) {
    //         $pop = Pop::find($request->input('id'));
    //         $pop->site_name = $request->site_name;
    //         $pop->site_description = $request->site_description;
    //         $pop->site_location = $request->site_location;
    //         $pop->update();
    //         if ($pop->id && $request->devices) {
    //             $pop_devices = PopDevice::where('pop_id', '=', $pop->id)->get()->toArray();
    //             // Extract 'id' values from the second array for comparison
    //             $secondArrayIds = array_column($request->devices, 'id');

    //             // Find IDs in the first array that are not in the second array
    //             $missingIds = array_filter(array_column($pop_devices, 'id'), function ($id) use ($secondArrayIds) {
    //                 return !in_array($id, $secondArrayIds);
    //             });
    //             PopDevice::whereIn('id', $missingIds)->delete();
    //             foreach ($request->devices as $device) {

    //                 if (isset($device['id'])) {
    //                     $existing_device = PopDevice::find($device['id']);
    //                     $existing_device->pop_id = $pop->id;
    //                     $existing_device->device_name = $device['device_name'];
    //                     $existing_device->qty = (isset($device['qty'])) ? $device['qty'] : 1;
    //                     $existing_device->remark = (isset($device['remark'])) ? $device['remark'] : null;
    //                     $existing_device->update();
    //                 } else {
    //                     $pop_device = new PopDevice();
    //                     $pop_device->pop_id = $pop->id;
    //                     $pop_device->device_name = $device['device_name'];
    //                     $pop_device->qty = (isset($device['qty'])) ? $device['qty'] : 1;
    //                     $pop_device->remark = (isset($device['remark'])) ? $device['remark'] : null;
    //                     $pop_device->save();
    //                 }
    //             }
    //         }
    //         return redirect()->back()
    //             ->with('message', 'POP Site Updated Successfully.');
    //     }
    // }
    public function update(Request $request, $id)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string',
            'site_location' => 'required|string',
            'partner_id' => 'required|exists:partners,id',
            'townships' => 'required|array',
        ]);

        $pop = Pop::findOrFail($request->input('id'));
        $pop->update($request->only(['site_name', 'site_description', 'site_location','partner_id']));
        $pop->townships()->sync(collect($request->townships)->pluck('id'));
        if ($request->has('devices')) {
            $pop_devices = PopDevice::where('pop_id', $pop->id)->pluck('id')->toArray();
            $secondArrayIds = array_column($request->devices, 'id');
            $missingIds = array_diff($pop_devices, $secondArrayIds);

            PopDevice::whereIn('id', $missingIds)->delete();

            foreach ($request->devices as $device) {
                PopDevice::updateOrCreate(
                    ['id' => $device['id'] ?? null, 'pop_id' => $pop->id],
                    [
                        'device_name' => $device['device_name'],
                        'qty' => $device['qty'] ?? 1,
                        'remark' => $device['remark'] ?? null
                    ]
                );
            }
        }

        return redirect()->back()->with('message', 'POP Site Updated Successfully.');
    }
    public function getPOPsByTownship($townshipId)
    {
        $pops = Pop::whereHas('townships', function($query) use ($townshipId) {
            $query->where('township_id', $townshipId);
        })->get();
        
        return response()->json($pops);
    }
  
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            Pop::find($request->input('id'))->delete();
            return redirect()->back()->with('message', 'POP Deleted Successfully.');
        }
    }
    public function deleteGroup(Request $request, $id)
    {
        if ($request->has('id')) {
            $customer = Customer::where('pop_id', $request->id)->first();
            if ($customer)
                return redirect()->back()->with('message', 'POP cannot delete due to foreign key constraint in Customer Database.');
            Pop::where('id', '=', $request->input('id'))->delete();
            return redirect()->back()->with('message', 'POP Deleted Successfully.');
        }
    }
}

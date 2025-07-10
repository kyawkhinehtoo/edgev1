<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\SnPorts;
use App\Models\DnPorts;
use App\Models\DnSplitter;
use App\Models\Odb;
use App\Models\OdbFiberCable;
use App\Models\Odf;
use App\Models\Pop;
use App\Models\PopDevice;
use App\Models\SnBox;
use App\Models\SnPort;
use App\Models\SnSplitter;
use Hamcrest\Arrays\IsArray;
use Hamcrest\Type\IsNumeric;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PortController extends Controller
{
    
    public function index(Request $request)
    {

        // $dns = DB::table('dn_ports')
        //     ->when($request->general, function ($query, $general) {
        //         $query->where('name', 'LIKE', '%' . $general . '%');
        //         $query->orwhere('description', 'LIKE', '%' . $general . '%');
        //     })
        //     ->paginate(10);
        $pops = Pop::all();
        $dns_all = DnPorts::get();
        $overall = DB::table('dn_ports')
        ->leftJoin('sn_ports', 'sn_ports.dn_id', '=', 'dn_ports.id')
        ->select(
            'dn_ports.id',
            'dn_ports.name',
            'dn_ports.description',
            'dn_ports.location',
            'dn_ports.pop',
            'dn_ports.input_dbm',
            DB::raw('count(sn_ports.id) as ports'),
            'dn_ports.pop_device_id',
            'dn_ports.gpon_frame',
            'dn_ports.gpon_slot',
            'dn_ports.gpon_port'
        )
        ->when($request->general, function ($query, $general) {
            $query->where('dn_ports.name', 'LIKE', '%' . $general . '%')
                  ->orWhere('dn_ports.description', 'LIKE', '%' . $general . '%');
        })
        ->when($request->pop, function ($query, $pops) {
            $pop_id = array_map(fn($pop) => $pop['id'], $pops);
            $query->where(function ($q) use ($pop_id) {
                foreach ($pop_id as $id) {
                    $q->orWhere('dn_ports.pop', $id);
                }
            });
        })
        ->groupBy(
            'dn_ports.id',
            'dn_ports.name',
            'dn_ports.description',
            'dn_ports.location',
            'dn_ports.pop',
            'dn_ports.input_dbm',
            'dn_ports.pop_device_id',
            'dn_ports.gpon_frame',
            'dn_ports.gpon_slot',
            'dn_ports.gpon_port'
        )
        ->orderBy('dn_ports.id')
        ->paginate(10);
        $overall->appends($request->all())->links();
        return Inertia::render(
            'Setup/Ports',
            ['overall' => $overall, 'dns_all' => $dns_all, 'pops' => $pops]
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
    public function getPopByPartner($request)
    {

        $pops = null;
        if ($request && is_numeric($request)) {
            $pops = Pop::where('partner_id', (int)$request)
                ->get();
        }
        return response()
            ->json($pops, 200);
    }
    public function getOLTByPOP($request)
    {

        $pop_devices = null;
        if ($request && is_numeric($request)) {
            $pop_devices = PopDevice::where('pop_id', (int)$request)
                ->get();
        }
        return response()
            ->json($pop_devices, 200);
    }
    public function getDNByOLT($request)
    {
        if ($request && is_numeric($request)) {
           
            $dns = DnSplitter::where('pop_device_id', (int)$request)->get();

                return response()
                ->json($dns, 200);
            
        }

        return response()
            ->json([], 200);
    }
    public function getFeederByOLT($request){
        if ($request && is_numeric($request)) {
           $fiberCables = OdbFiberCable::join('fiber_cables','fiber_cables.id', '=', 'odb_fiber_cables.fiber_cable_id')
            ->where('pop_device_id', (int)$request)
            ->select('fiber_cables.name as name','fiber_cables.id as id')
            ->groupBy('fiber_cables.name','fiber_cables.id')
            ->orderby('fiber_cables.name')
            ->get();
           return response()->json($fiberCables, 200);
        }
        return response()->json([], 200);
    }
    // public function getDNByOLT($request)
    // {
    //     if ($request && is_numeric($request)) {
           
    //         // Get all OdbFiberCable connections associated with these ODBs or directly with the PopDevice
    //         $odbFiberCables = OdbFiberCable::where(function($query) use ($request) {
    //                 $query->where('pop_device_id', (int)$request);
    //             })
    //             ->get();
    //        $dnArray = [];
    //             foreach($odbFiberCables as $odbFiberCable){
    //                 $dnSplitters = DnSplitter::where('fiber_id', $odbFiberCable->fiber_cable_id)
    //                 ->where('core_number',$odbFiberCable->odb_port)
    //                 ->where('status', 'active')
    //                 ->get();
    //                 if($dnSplitters->isNotEmpty()){
    //                     foreach($dnSplitters as $dnSplitter){
    //                         $dnArray[] = $dnSplitter;
    //                     }
    //                 }
    //             }
    //         // Get all DN Splitters associated with these fiber cables
           
    
    //         // If you also want to include SN Boxes and SN Splitters in the response

    //             return response()
    //             ->json($dnArray, 200);
            
    //     }

    //     return response()
    //         ->json([], 200);
    // }
    // public function getDnSplitterByOLT($request)
    // {
    //     $dnSplitters = null;

    //     if ($request && is_numeric($request)) {
    //         // Get all ODFs associated with this PopDevice (OLT)
    //         $odfIds = Odf::where('pop_device_id', 'like', '%' . (int)$request . '%')
    //             ->pluck('id');
            
    //         // Get all ODBs associated with these ODFs
    //         $odbIds = Odb::whereIn('odf_id', $odfIds)
    //             ->pluck('id');
            
    //         // Get all OdbFiberCable connections associated with these ODBs or directly with the PopDevice
    //         $fiberCableIds = OdbFiberCable::where(function($query) use ($odbIds, $request) {
    //                 $query->whereIn('odb_id', $odbIds)
    //                       ->Where('pop_device_id', (int)$request);
    //             })
    //             ->pluck('fiber_cable_id')
    //             ->unique();
    //     //    dd($fiberCableIds);
    //         // Get all DN Splitters associated with these fiber cables
    //         $dnSplitters = DnSplitter::with(['dnBox', 'fiberCable'])
    //             ->whereIn('fiber_id', $fiberCableIds)
    //             ->where('status', 'active')
    //             ->get();
    
    //         // If you also want to include SN Boxes and SN Splitters in the response
    //         if ($dnSplitters->isNotEmpty()) {
    //             $dnSplitterIds = $dnSplitters->pluck('id');
                
    //             // Get all SN Boxes associated with these DN Splitters
    //             $snBoxes = SnBox::with(['dnSplitter'])
    //                 ->whereIn('dn_splitter_id', $dnSplitterIds)
    //                 ->where('status', 'active')
    //                 ->get();
               
    //             // Get all SN Splitters associated with these SN Boxes
    //             if ($snBoxes->isNotEmpty()) {
    //                 $snBoxIds = $snBoxes->pluck('id');
                    
    //                 $snSplitters = SnSplitter::with(['snBox'])
    //                     ->whereIn('sn_id', $snBoxIds)
    //                     ->where('status', 'active')
    //                     ->get();
      
    //                 // Add SN Boxes and SN Splitters to the response
    //                 return response()->json([
    //                     'dnSplitters' => $dnSplitters,
    //                     'snBoxes' => $snBoxes,
    //                     'snSplitters' => $snSplitters
    //                 ], 200);
    //             }
    //         }
    //     }
        
    //     return response()->json(['dnSplitters' => $dnSplitters], 200);
    // }
    public function getSNByDN($request)
    {

        $sn = null;
        if ($request && is_numeric($request)) {
            $snBoxes = SnBox::with(['dnSplitter'])
            ->where('dn_splitter_id', $request)
            ->where('status', 'active')
            ->get();
            if ($snBoxes->isNotEmpty()) {
                $snBoxIds = $snBoxes->pluck('id');
                
                $snSplitters = SnSplitter::with(['snBox'])
                    ->whereIn('sn_id', $snBoxIds)
                    ->where('status', 'active')
                    ->get();
  
                // Add SN Boxes and SN Splitters to the response
                return response()
            ->json($snSplitters, 200);
            }
                   }
        return response()
            ->json([], 200);
    }
    public function getDNInfo($request)
    {
        $dn = null;
        if ($request && is_numeric($request)) {
            $dn = DB::table('dn_ports')
                ->where('dn_ports.id', '=', $request)
                ->select(
                    'dn_ports.name as dn_name',
                    'dn_ports.pop as pop_id',
                    'dn_ports.pop_device_id as pop_device_id',
                    'dn_ports.gpon_frame',
                    'dn_ports.gpon_slot',
                    'dn_ports.gpon_port'
                )
                ->get();
        }
        return response()
            ->json($dn, 200);
    }

    public function getAvailablePortBySplitterId($request){
        // Initialize port list with numbers 1-16
        $portList = [];
        for($n=1; $n <=16 ; $n++ ){
            $portList[] = [
                'id' => $n,
                'name' => 'SN Port : '.(string)$n
            ];
        }

        // Get the port numbers that are already in use for this splitter
        $usedPorts = SnPort::where('sn_splitter_id', $request)
                    ->pluck('port_number')
                    ->toArray();
        
        // Mark used ports as disabled instead of removing them
        foreach($portList as &$port) {
            if(in_array($port['id'], $usedPorts)) {
                $port['$isDisabled'] = true;
            }
        }
        
        // Return the complete port list with disabled flags as a JSON response
        return response()
            ->json($portList, 200);
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'location' => ['required'],
            'input_dbm' => ['required'],
        ])->validate();


        $check_dup = DnPorts::where('name', $request->name)
            ->exists();
        if ($check_dup) {
            return redirect()->back()->withErrors('DN Already Exist!');
        } else {

            $dnport = new DnPorts();
            $dnport->name = $request->name;
            $dnport->pop = $request->pop ? $request->pop['id'] : null;
            $dnport->pop_device_id = $request->pop_device_id ? $request->pop_device_id['id'] : null;
            $dnport->gpon_frame = $request->gpon_frame;
            $dnport->gpon_slot = $request->gpon_slot;
            $dnport->gpon_port = $request->gpon_port;
            $dnport->description = $request->description;
            $dnport->location = $request->location;
            $dnport->input_dbm = $request->input_dbm;
            $dnport->save();
            return redirect()->back()->with('message', 'DN Port Created Successfully.');
        }

        // }else{
        // for($n=1; $n <=$request->port ; $n++ ){
        //     $dnport = new DnPorts();
        //     $dnport->name = $request->name;
        //     $dnport->port = $n;
        //     $dnport->description = $request->description;
        //     $dnport->save();
        // }

        //  return redirect()->back()->with('message', 'DN Created Successfully.');
        // }
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'location' => ['required'],
            'input_dbm' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            $dnport = DnPorts::find($request->input('id'));
            $dnport->name = $request->name;
            $dnport->pop = $request->pop ? $request->pop['id'] : null;
            $dnport->pop_device_id = $request->pop_device_id ? $request->pop_device_id['id'] : null;
            $dnport->gpon_frame = $request->gpon_frame;
            $dnport->gpon_slot = $request->gpon_slot;
            $dnport->gpon_port = $request->gpon_port;
            $dnport->description = $request->description;
            $dnport->location = $request->location;
            $dnport->input_dbm = $request->input_dbm;
            $dnport->update();
            return redirect()->back()
                ->with('message', 'Port Updated Successfully.');
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {

            $customer = Customer::join('sn_ports', 'customers.sn_id', 'sn_ports.id')
                ->join('dn_ports', 'sn_ports.dn_id', 'dn_ports.id')
                ->where('dn_ports.id', $request->id)->first();
            if ($customer)
                return redirect()->back()->with('message', 'DN cannot delete due to foreign key constraint in Customer Database.');
            DnPorts::find($request->input('id'))->delete();
            return redirect()->back()->with('message', 'DN Deleted Successfully.');
        }
    }
    public function deleteGroup(Request $request, $id)
    {
        if ($request->has('id')) {
            $customer = Customer::join('sn_ports', 'customers.sn_id', 'sn_ports.id')
                ->join('dn_ports', 'sn_ports.dn_id', 'dn_ports.id')
                ->where('dn_ports.id', $request->id)->first();
            if ($customer)
                return redirect()->back()->with('message', 'DN cannot delete due to foreign key constraint in Customer Database.');
            DnPorts::where('name', '=', $request->input('id'))->delete();
            return redirect()->back()->with('message', 'DN Deleted Successfully.');
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\BundleEquiptment;
use App\Models\City;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Project;
use App\Models\User;
use App\Models\Status;
use App\Models\Township;
use App\Models\Role;
use App\Models\SnPorts;
use App\Models\DnPorts;
use App\Models\CustomerHistory;
use App\Models\FileUpload;
use App\Models\Isp;
use App\Models\Partner;
use App\Models\Pop;
use App\Models\PopDevice;
use App\Models\PublicIpAddress;
use App\Models\Subcom;
use Carbon\Carbon;
use Inertia\Inertia;
use DateTime;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->show($request);
    }
    public function show(Request $request)
    {
        //   dd($request

        $user = User::with('role')->where('users.id', '=', Auth::user()->id)->first();
        $packages = Package::get();
        $townships = Township::get();
        $partners = Partner::get();
        $status = Status::get();
        $isps = Isp::get();
        $dn = DnPorts::get();
        $onuSerials = Customer::where('customers.deleted', '=', 0)
            ->orWhereNull('customers.deleted')
            ->whereNotNull('onu_serial')
            ->select('onu_serial')
            ->groupBy('onu_serial')
            ->orderBy('onu_serial')
            ->get();
        $installationTeams = Subcom::all();
        $bundle_equiptments = BundleEquiptment::get();
        $active = DB::table('customers')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->whereIn('status.type', ['active', 'disabled'])
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->when($user->user_type, function ($query, $user_type) use ($user) {
                if($user_type == 'partner') {
                    $query->where('customers.partner_id', '=', $user->partner_id);
                }
                if($user_type == 'isp') {
                    $query->where('customers.isp_id', '=', $user->isp_id);
                }
                if($user_type == 'subcon') {
                    $query->where('customers.subcom_id', '=', $user->subcom_id);
                }
           
            })
            ->count();

        // $relocation = DB::table('customers')
        // ->join('status', 'customers.status_id', '=', 'status.id')
        // ->where('status.name', 'LIKE', '%Relocation%')
        // ->where('customers.deleted', '<>', '1')
        // ->count();
        $suspense = DB::table('customers')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->where('status.type', '=', 'suspense')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->when($user->user_type, function ($query, $user_type) use ($user) {
                if($user_type == 'partner') {
                    $query->where('customers.partner_id', '=', $user->partner_id);
                }
                if($user_type == 'isp') {
                    $query->where('customers.isp_id', '=', $user->isp_id);
                }
                if($user_type == 'subcon') {
                    $query->where('customers.subcom_id', '=', $user->subcom_id);
                }
           
            })
            ->count();
        $installation_request = DB::table('customers')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->where('status.type', '=', 'new')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->when($user->user_type, function ($query, $user_type) use ($user) {
                if($user_type == 'partner') {
                    $query->where('customers.partner_id', '=', $user->partner_id);
                }
                if($user_type == 'isp') {
                    $query->where('customers.isp_id', '=', $user->isp_id);
                }
                if($user_type == 'subcon') {
                    $query->where('customers.subcom_id', '=', $user->subcom_id);
                }
           
            })
            ->count();
        $terminate = DB::table('customers')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->where('status.type', '=', 'terminate')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->when($user->user_type, function ($query, $user_type) use ($user) {
                if($user_type == 'partner') {
                    $query->where('customers.partner_id', '=', $user->partner_id);
                }
                if($user_type == 'isp') {
                    $query->where('customers.isp_id', '=', $user->isp_id);
                }
                if($user_type == 'subcon') {
                    $query->where('customers.subcom_id', '=', $user->subcom_id);
                }
           
            })
            ->count();


        $orderform = null;
        if ($request->orderform)
            $orderform['status'] = ($request->orderform == 'signed') ? 1 : 0;

        $all_township = Township::select('id')
            ->get()
            ->toArray();
        $all_packages = Package::select('id')
            ->get()
            ->toArray();
        $package_speed = Package::select('speed', 'type')
            ->groupBy('speed', 'type')
            ->orderBy('speed', 'ASC')->get();
         //dd($request);
        $customers =  Customer::with('package','township','isp','sn','dn','pop','pop_device','status')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->when($user->user_type, function ($query, $user_type) use ($user) {
                if($user_type == 'partner') {
                    $query->where('customers.partner_id', '=', $user->partner_id);
                }
                if($user_type == 'isp') {
                    $query->where('customers.isp_id', '=', $user->isp_id);
                }
                if($user_type == 'subcon') {
                    $query->where('customers.subcom_id', '=', $user->subcom_id);
                }
           
            })
            ->when($request->keyword, function ($query, $search = null) {
                $query->where(function ($query) use ($search) {
                    $query->where('customers.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $search . '%');
                });
            })->when($request->general, function ($query, $general) {
                $query->where(function ($query) use ($general) {
                    $query->where('customers.name', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%');
                });
            })
            ->when($request->installation, function ($query, $installation) {
                $startDate = Carbon::parse($installation[0])->format('Y-m-d');
                $endDate = Carbon::parse($installation[1])->format('Y-m-d');
                $query->whereBetween('customers.installation_date', [$startDate, $endDate]);
            })
            ->when($request->order, function ($query, $order) {
                $startDate = Carbon::parse($order[0])->format('Y-m-d');
                $endDate = Carbon::parse($order[1])->format('Y-m-d');
                $query->whereBetween('customers.order_date',  [$startDate, $endDate]);
            })
            ->when($request->prefer, function ($query, $prefer) {
                $startDate = Carbon::parse($prefer[0])->format('Y-m-d');
                $endDate = Carbon::parse($prefer[1])->format('Y-m-d');
                $query->whereBetween('customers.prefer_install_date', [$startDate, $endDate]);
            })
            ->when($request->dn, function ($query, $dn_2) {
                $query->where('customers.dn_id', '=', $dn_2['id']);
            })
            ->when($request->sn, function ($query, $sn) {
                $query->where('customers.sn_id', '=', $sn['id']);
            })
            ->when($request->pop, function ($query, $pop) {
                $query->where('customers.pop_id', '=', $pop['id']);
            })
            ->when($request->pop_device, function ($query, $pop_device) {
                $query->where('customers.pop_device_id', '=', $pop_device['id']);
            })
            ->when($request->package, function ($query, $package) use ($all_packages) {
                if ($package == 'empty') {
                    $query->whereNotIn('customers.package_id', $all_packages);
                } else {
                    $query->where('customers.package_id', '=', $package);
                }
            })
            ->when($request->package_speed, function ($query, $package_speed) {
                $speed_type =  explode("|", $package_speed);
                $speed = $speed_type[0];
                $type = $speed_type[1];
                $query->whereHas('package', function($q) use ($speed, $type) {
                    $q->where('speed', '=', $speed);
                    $q->where('type', '=', $type);
                });
            })
            ->when($request->township, function ($query, $township) use ($all_township) {
                if ($township == 'empty') {
                    $query->whereNotIn('customers.township_id', $all_township);
                } else {
                    $query->where('customers.township_id', '=', $township);
                }
            })
            ->when($request->status, function ($query, $status) {
                $query->where('customers.status_id', '=', $status);
            })
            ->when($request->sh_isp, function ($query, $sh_isp) {
                $query->where('customers.isp_id', '=', $sh_isp['id']);
            })
            ->when($request->partner, function ($query, $partner) {
                $query->where('customers.partner_id', '=', $partner['id']);
            })
            ->when($request->status_type, function ($query, $status_type) {
                $query->whereHas('status', function($q) use ($status_type) {
                    $q->where('type', '=', $status_type);
                });
            })
            ->when($request->order, function ($query, $order) {
                $query->whereBetween('customers.order_date', $order);
            })
            ->when($request->installation, function ($query, $installation) {
                $query->whereBetween('customers.installation_date', $installation);
            })

            // ->when($request->sh_vlan, function ($query, $vlan) {
            //     $query->where('customers.vlan', $vlan);
            // })

            ->when($request->sh_onu_serial, function ($query, $sh_onu_serial) {
                $query->where('customers.onu_serial', $sh_onu_serial);
            })
            ->when($request->sh_installation_team, function ($query, $sh_installation_team) {
                $query->where('customers.subcom_id', $sh_installation_team['id']);
            })
            
            ->when($request->assign_date, function ($query, $assign_date) {
                $startDate = Carbon::parse($assign_date[0])->format('Y-m-d');
                $endDate = Carbon::parse($assign_date[1])->format('Y-m-d');
                $query->whereBetween('customers.subcom_assign_date', [$startDate, $endDate]);
    
            })
            ->when($request->installation_status, function ($query, $sh_installation_status) {

                $query->where('customers.installation_status', $sh_installation_status);
            })
            ->when($request->sh_installation_timeline, function ($query, $sh_installation_timeline) {
            
                $query->where('customers.installation_timeline', $sh_installation_timeline);
            })
            

            ->when($request->sort, function ($query, $sort = null) {
                $sort_by = 'customers.id';
                if ($sort == 'cid') {
                    $sort_by = 'customers.id';
                } elseif ($sort == 'cname') {
                    $sort_by = 'customers.name';
                } elseif ($sort == 'township') {
                    $sort_by = 'customers.township.name';
                } elseif ($sort == 'package') {
                    $sort_by = 'customers.package.name';
                } elseif ($sort == 'order') {
                    $sort_by = 'customers.order_date';
                }

                $query->orderBy($sort_by, 'desc');
            }, function ($query) {
                $query->orderBy('customers.ftth_id', 'desc');
            })
            ->paginate(10);
            $dynamicRanderPage = "Client/Customer";
            if($user->user_type == 'subcon') {
                $dynamicRanderPage = "Client/CustomerSubcom";
            }
       
        // dd($customers->toSQL(), $customers->getBindings());
        $customers->appends($request->all())->links();
        return Inertia::render($dynamicRanderPage, [
            'packages' => $packages,
            'package_speed' => $package_speed,
            'townships' => $townships,
            'status' => $status,
            'customers' => $customers,
            'dn' => $dn,
            'active' => $active,
            'suspense' => $suspense,
            'installation_request' => $installation_request,
            'terminate' => $terminate,
            'user' => $user,
            'bundle_equiptments' => $bundle_equiptments,
            'installationTeams' => $installationTeams,
            'onuSerials' => $onuSerials,
            'isps' => $isps,
            'partners'=>$partners,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $packages = Package::get();
        $sn = SnPorts::get();
        $projects = Project::get();
        $pops = Pop::get();
        $bundle_equiptments = BundleEquiptment::get();
        $dn = DB::table('dn_ports')
            ->get();
     
        $auth_role = DB::table('users')
            ->join('roles', 'users.role', '=', 'roles.id')
            ->where('roles.name', 'NOT LIKE', '%Admin%')
            ->where('users.id', '=', Auth::user()->id)
            ->select('users.name as name', 'users.id as id')
            ->get();

        if (!$auth_role->isEmpty()) {
            $sale_persons = $auth_role;
        }
        $userPerm = $this->getPermision();
        $subcoms = Subcom::all();
        $isps = Isp::all();
        $partners = Partner::all();
        $townships = Township::join('cities', 'townships.city_id', '=', 'cities.id')->select('townships.*', 'cities.name as city_name', 'cities.short_code as city_code', 'cities.id as city_id')->get();
        $status_list = $this->getStatusList();
        $max_id = $this->getmaxid();
        $user = User::with('role')->find(Auth::user()->id);
        return Inertia::render(
            'Client/AddCustomer',
            [
                'packages' => $packages,
                'projects' => $projects,
                'townships' => $townships,
                'status_list' => $status_list,
                'subcoms' => $subcoms,
                'user' => $user,
                'userPerm' => $userPerm,
                'sn' => $sn,
                'dn' => $dn,
                'pops' => $pops,
                'max_id' => $max_id,
                'partners' => $partners,
                'isps' => $isps,
                'bundle_equiptments' => $bundle_equiptments,
            ]
        );
    }

    public function store(Request $request)
    {
        $user = User::with('role')->find(Auth::user()->id);
        $userPerms = $this->getPermision();

        Validator::make($request->all(), [
            'name' => 'required|max:255',
            'phone_1' => 'required|max:255',
            'address' => 'required',
            'latitude' => 'required|max:255',
            'longitude' => 'required|max:255',
            'package' => 'required',
            'ftth_id' => 'required|max:255',
            'dob' => 'nullable|date',
            'status' => 'required',
            'order_date' => 'date',
            'township' => 'required',
            'installation_date' => 'nullable|date',

        ])->validate();


        $auto_ftth_id = $request->ftth_id;
        $check_id = Customer::where('ftth_id', '=', $auto_ftth_id)->first();
        if ($check_id) {
            //already exists
            if ($request->township && $request->package) {
                $max_c_id = $this->getmaxid();
                $city_id = $request->township['city_id'];
                $pacakge_type = $request->package['type'];
                $result = null;
                foreach ($max_c_id as $value) {
                    if ((int)$value['id'] == (int)$city_id) {
                        $result = $value['value'];
                    }
                }
                if ($result) {
                    //   $max_id = $max_c_id [$request->city_id];
                    $auto_ftth_id = $request->township['city_code'] . str_pad($result + 1, 6, '0', STR_PAD_LEFT) . 'FX';
                }
            }
        }
        $customer = new Customer();
        foreach ($userPerms as $key => $value) {
            if ($value != 'id' && $value!= 'created_at' && $value!= 'updated_at' && $value!= 'deleted')
                $customer->$value = $request->$value;

            if ($value == 'ftth_id')
                $customer->$value = $auto_ftth_id;
            if ($value == 'location')
                $customer->$value = $request->latitude . ',' . $request->longitude;
            if ($value == 'status_id')
                $customer->$value = $request->status['id'];
            if ($value == 'township_id')
                $customer->$value = $request->township['id'];
            if ($value == 'package_id')
                $customer->$value = $request->package['id'];
            if ($value == 'sale_person_id')
                $customer->$value = $request->sale_person['id'];
            if ($value == 'subcom_id') {
                if (!empty($request->subcom))
                    $customer->$value = $request->subcom['id'];
            }
            if ($value == 'project_id') {
                if (!empty($request->project))
                    $customer->$value = $request->project['id'];
            }
            if ($value == 'sn_id') {
                if (!empty($request->sn_id))
                    $customer->$value = $request->sn_id['id'];
            }
            if ($value == 'dn_id') {
                if (!empty($request->dn_id))
                    $customer->$value = $request->dn_id['id'];
            }
            if ($value == 'pop_id') {
                if (!empty($request->pop_id))
                    $customer->$value = $request->pop_id['id'];
            }
            if ($value == 'pop_device_id') {
                if (!empty($request->pop_device_id))
                    $customer->$value = $request->pop_device_id['id'];
            }
            if ($value == 'splitter_no') {
                if (isset($request->splitter_no['id']))
                    $customer->$value = $request->splitter_no['id'];
            }
            if ($value == 'gpon_ontid') {
                if (isset($request->gpon_ontid['name']))
                    $customer->$value = $request->gpon_ontid['name'];
            }
            if ($value == 'partner_id') {
                if (isset($request->partner_id['id']))
                    $customer->$value = $request->partner_id['id'];
            }
            if ($value == 'isp_id') {
                if (isset($request->isp_id['id']))
                    $customer->$value = $request->isp_id['id'];
            }
            if($user->user_type == 'isp') {
                $customer->isp_id = $user->isp_id;
            }
            if ($value == 'bundle') {
                if (!empty($request->bundles)) {
                    $customer->bundle = '';
                    foreach ($request->bundles as $key => $value) {
                        if ($key !== array_key_last($request->bundles))
                            $customer->bundle .= $value['id'] . ',';
                        else
                            $customer->bundle .= $value['id'];
                    }
                }
            }
        }
        $customer->deleted = 0;

        $customer->save();

       
       
     
        $logData = [];
      //  $changes = $customer->getChanges();
      $changes = $customer->getAttributes();
 
        app(\App\Services\CustomerHistoryService::class)->storeCustomerHistory(
            $customer,         // newly updated Customer
            null,      // old snapshot
            $changes,          // the changes array
            $customer->id,
            $request->input('start_date') // optional
        );
        foreach ($changes as $key => $newValue) {
            $logData[$key] = [
                'to' => $newValue                   // New value
            ];
        }
        activity()
            ->causedBy(User::find(Auth::id()))
            ->performedOn($customer)
            ->withProperties(['changes' => $logData])  // Log the changes with from-to values
            ->log('Customer Created: ' . $customer->ftth_id);
        return redirect()->route('customer.index')->with('message', 'Customer Created Successfully.');
    }

    public function getPermision():Array
    {
        $user = User::find(Auth::user()->id);
        switch ($user->user_type) {
            case 'partner':
                $partner = Partner::find($user->partner_id);
                return $partner && $partner->permissions ? $partner->permissions : [];
                break;
            case 'isp':
                $isp = Isp::find($user->isp_id);
                return $isp && $isp->permissions ? $isp->permissions : [];
                break;
            case 'subcom':
                $subcom = Subcom::find($user->subcom_id);
                return $subcom && $subcom->permissions ? $subcom->permissions : [];
                break;
            case 'internal':
                $role = Role::find($user->role);
                return $role && $role->permissions ? $role->permissions : [];
                break;
            default:
                return [];
                break;
        }
    }
    public function getStatusList():Array
    {
        $user = User::find(Auth::user()->id);
        switch ($user->user_type) {
            case 'partner':
                $partner = Partner::find($user->partner_id)->first();
                return $partner && $partner->customer_status ? $partner->customer_status : [];
                break;
            case 'isp':
                $isp = Isp::find($user->isp_id)->first();
                return $isp && $isp->customer_status ? $isp->customer_status : [];
                break;
            case 'internal':
                $role = Role::find($user->role)->first();
                return $role && $role->customer_status ? $role->customer_status : [];
                break;
            default:
                return [];
                break;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!$id) {
            return redirect()->back()->with('error', 'Invalid customer ID.');
        }
        $user = User::with('role')->find(Auth::user()->id);

        $typePermissions = [
            'subcon' => ['model' => Subcom::class, 'field' => 'subcom_id'],
            'isp' => ['model' => Isp::class, 'field' => 'isp_id'],
            'partner' => ['model' => Partner::class, 'field' => 'partner_id']
        ];

        if (isset($typePermissions[$user->user_type])) {
            $permission = $typePermissions[$user->user_type];
            $model = $permission['model'];
            $field = $permission['field'];
            
            $entity = $model::find($user->{$field});
            $hasAccess = Customer::where(function ($query) {
                    $query->where('deleted', 0)->orWhereNull('deleted');
                })
                ->where($field, $entity->id)
                ->where('id', $id)
                ->exists();

            if (!$hasAccess) {
                return abort(403, 'Unauthorized access.');
            }
        }
        $customer = Customer::with('sn','dn','pop','pop_device','township','partner','package','isp')
            ->where(function ($query) {
                $query->where('deleted', 0)->orWhereNull('deleted');
            })->find($id);
        $bundle_equiptments = BundleEquiptment::get();
            
        if($user->user_type == 'subcon') {
            return Inertia::render('Client/SubcomCustomerView', [
                'customer' => $customer,
                'bundle_equiptments'=>$bundle_equiptments
            ]);
        }
        
        $customer_history = CustomerHistory::where('customer_id', '=', $id)
            ->where('active', '=', 1)
            ->first();
        $isps = Isp::all();
        $partners = Partner::all();
        $pops = Pop::all();
        $packages = Package::get();
        $projects = Project::get();
       
        $subcoms = Subcom::all();
        $townships = Township::join('cities', 'townships.city_id', '=', 'cities.id')->select('townships.*', 'cities.name as city_name', 'cities.short_code as city_code', 'cities.id as city_id')->get();
        $allStatus = Status::get()->toArray();
        $status_list = $this->getStatusList();
        
        $userPerm = $this->getPermision();
       
       
        
      
        $total_documents = FileUpload::where('customer_id', $customer->id)->whereNull('incident_id')->count();
       
        return Inertia::render(
            'Client/EditCustomer',
            [
                'customer' => $customer,
                'packages' => $packages,
                'projects' => $projects,
                'allStatus' => $allStatus,
                'townships' => $townships,
                'status_list' => $status_list,
                'subcoms' => $subcoms,
                'user' => $user,
                'userPerm' => $userPerm,
                'customer_history' => $customer_history,
        
                'pops' => $pops,
                'partners' => $partners,
                'isps' => $isps,
                'total_documents' => $total_documents,
                'bundle_equiptments' => $bundle_equiptments,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::with('role')->find(Auth::user()->id);
        $userPerms = $this->getPermision();


        Validator::make($request->all(), [
            'name' => 'required|max:255',
            'phone_1' => 'required|max:255',
            'address' => 'required',
            'latitude' => 'required|max:255',
            'longitude' => 'required|max:255',
            'package' => 'required',
            'ftth_id' => 'required|max:255',
            'dob' => 'nullable|date',
            'order_date' => 'date',
            'status' => 'required',
            'installation_date' => 'nullable|date',
            // 'route_kmz_image' => 'nullable|image|max:10240',
            // 'drum_no_image' => 'nullable|image|max:10240',
            // 'start_meter_image' => 'nullable|image|max:10240',
            // 'end_meter_image' => 'nullable|image|max:10240',

        ])->validate();
        if ($request->has('id') && !$user?->roles?->read_customer && $userPerms ){
            $customer = Customer::find($request->input('id'));
            $oldCustomer = clone $customer;

            // Handle image uploads
            $imageFields = ['route_kmz_image', 'drum_no_image', 'start_meter_image', 'end_meter_image'];
            
            foreach ($imageFields as $imageField) {
                if ($request->hasFile($imageField)) {
                    // Delete old image if exists
                    if ($customer->$imageField) {
                        $oldPath = public_path('storage/' . $customer->$imageField);
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                    
                    // Get the uploaded file
                    $file = $request->file($imageField);
                    
                    // Create directory if it doesn't exist
                    $directory = 'customer_images/' . $customer->id;
                    $storagePath = public_path('storage/' . $directory);
                    if (!file_exists($storagePath)) {
                        mkdir($storagePath, 0755, true);
                    }
                    
                    // Generate a unique filename
                    $filename = $imageField . '_' . time() . '.' . $file->getClientOriginalExtension();
                    
                    // Move the file from temp location to the storage path
                    $file->move($storagePath, $filename);
                    
                    // Save the relative path to the database (not the temp path)
                    $customer->$imageField = $directory . '/' . $filename;
                }
            }

            foreach ($userPerms as $key => $value) {
                if ($value != 'id' && $value!= 'created_at' && $value!= 'updated_at' && $value!= 'deleted')
                    $customer->$value = $request->$value;

                if ($value == 'location')
                    $customer->$value = $request->latitude . ',' . $request->longitude;
                if ($value == 'status_id') 
                    $customer->$value = $request->status?json_decode($request->status)?->id:null;
                if ($value == 'township_id')
                    $customer->$value = $request->township?json_decode($request->township)?->id:null;
                if ($value == 'package_id')
                    $customer->$value = $request->package?json_decode($request->package)?->id:null;
           
                if ($value == 'project_id') {
                    if (!empty($request->project))
                        $customer->$value =  $request->project?json_decode($request->project)?->id:null;
                }
                if ($value == 'sn_id') {
                    if (!empty($request->sn_id))
                        $customer->$value = $request->sn_id?json_decode($request->sn_id)?->id:null;
                }
                if ($value == 'dn_id') {
                    if (!empty($request->dn_id))
                        $customer->$value =  $request->dn_id?json_decode($request->dn_id)?->id:null;
                }
                if ($value == 'pop_id') {
                    if (isset($request->pop_id))
                        $customer->$value =  $request->pop_id?json_decode($request->pop_id)?->id:null;
                }
                if ($value == 'pop_device_id') {
                    if (isset($request->pop_device_id))
                        $customer->$value =  $request->pop_device_id?json_decode($request->pop_device_id)?->id:null;
                }
                if ($value == 'splitter_no') {
                    if (isset($request->splitter_no))
                        $customer->$value =  $request->splitter_no?json_decode($request->splitter_no)?->id:null;
                }
                if ($value == 'gpon_ontid') {
                    if (isset($request->gpon_ontid))
                        $customer->$value =  $request->gpon_ontid?json_decode($request->gpon_ontid)?->name:null;
                }
                if ($value == 'partner_id') {
                    if (isset($request->partner_id))
                    $customer->$value =  $request->partner_id?json_decode($request->partner_id)?->id:null;
                }
                if ($value == 'isp_id') {
                 
                    if (isset($request->isp_id))
                    $customer->$value =  $request->isp_id?json_decode($request->isp_id)?->id:null;
                }
                if ($value == 'bundle') {
                    if (!empty($request->bundles)) {
                        $bundle_list = json_decode($request->bundles) ;
                        $customer->bundle = '';
                        foreach ($bundle_list as $key => $value) {
    
                            if ($key !== array_key_last($bundle_list))
                                $customer->bundle .= $value->id. ',';
                            else
                                $customer->bundle .= $value->id;
                        }
                    }
                }
                //Subcom info
                if ($value == 'installation_status') {
                    $customer->$value =  $request->installation_status?json_decode($request->installation_status)?->id:null;
                }
               
                if ($value == 'subcom_id') {
                        $customer->$value =  $request->subcom?json_decode($request->subcom)?->id:null;       
                }
                if($value == 'subcom_assign_date'){
                    if (!empty($request->subcom)){
                        if(!$customer->subcom_assign_date){
                            $customer->$value = Carbon::now()->format('Y-m-d');
                        }
                    }
                }
           
                
            }
            $original = $customer->getOriginal();  // Get the original values before update
            $customer->update();                   // Perform the update
            $changes = $customer->getChanges();    // Get the updated values after the update
            app(\App\Services\CustomerHistoryService::class)->storeCustomerHistory(
                $customer,         // newly updated Customer
                $oldCustomer,      // old snapshot
                $changes,          // the changes array
                $request->input('id'),
                $request->input('start_date') // optional
            );
            $logData = [];
            foreach ($changes as $key => $newValue) {
                $logData[$key] = [
                    'from' => $original[$key] ?? null,  // Original value
                    'to' => $newValue                   // New value
                ];
            }
            activity()
                ->causedBy(User::find(Auth::id()))
                ->performedOn($customer)
                ->withProperties(['changes' => $logData])  // Log the changes with from-to values
                ->log('Customer updated: ' . $customer->ftth_id);

          
            return redirect()->back()->with('message', 'Customer Updated Successfully.');
        }
        return redirect()->back()->with('message', 'No Changed has updated.');

        
    }
    public function getHistory($id)
    {
        if ($id) {
            $customer_history =  CustomerHistory::where('customer_id', '=', $id)
                ->leftjoin('status', 'status.id', '=', 'customer_histories.old_status')
                ->leftjoin('status as s', 's.id', '=', 'customer_histories.new_status')
                ->join('users', 'users.id', '=', 'customer_histories.actor_id')
                ->leftjoin('packages as p1', DB::raw('p1.id'), '=', 'customer_histories.old_package')
                ->leftjoin('packages as p2', DB::raw('p2.id'), '=', 'customer_histories.new_package')
                ->select('customer_histories.*', 'status.name as old_status_name', 'status.color as status_color', 'users.name as actor_name', DB::raw('p1.name as old_package_name'), DB::raw('p2.name as new_package_name'), DB::raw('s.name as new_status_name'))
                ->OrderBy('customer_histories.id', 'DESC')
                ->get();
            return response()->json($customer_history, 200);
        }
    }
    public function getmaxid()
    {
        $customers = Customer::where(function ($query) {
            return $query->where('customers.deleted', '=', 0)
                ->orWhereNull('customers.deleted');
        })->get();
        $cities = City::all();
        $max_c_id = array();

        foreach ($cities as $city) {

            $cid = array();
            foreach ($customers as $customer) {
                ///(^TCL[0-9]{5}-[A-Z]{3,})$/
                $reg = "/(^" . $city->short_code . "[0-9]{6}[A-Z 0-9]{2,})$/";
                if (preg_match($reg, $customer->ftth_id)) {
                    $pattern = '/\d+/'; // Regular expression to match integers
                    preg_match($pattern, $customer->ftth_id, $matches);

                    if (isset($matches[0])) {
                        $integer = (int)$matches[0];
                        array_push($cid, $integer);
                    }
                }
            }
            if (!empty($cid)) {
                $max_id = max($cid);
                //  dd($max_id);
            } else {
                $max_id = 0;
            }


            $id_array = array('id' => $city->id, 'value' => $max_id);

            array_push($max_c_id, $id_array);
        }

        //   if(sizeof($max_c_id))
        //dd($max_c_id);
        return $max_c_id;
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function syncRadius()
    {
        $customers = Customer::where('customers.deleted', '=', 0)
            ->orWhereNull('customers.deleted')
            ->get();
        if (RadiusController::checkRadiusEnable()) {
            foreach ($customers as $customer) {
                if ($customer->pppoe_account) {
                    $radius = RadiusController::checkCustomer($customer->pppoe_account);
                    if ($radius != 'no account') {
                        RadiusController::setExpiry($customer->pppoe_account, $customer->service_off_date);
                        //RadiusController::createRadius($customer->id);
                        echo $customer->pppoe_account . ' Created! <br />';
                    } else {
                        echo $customer->pppoe_account . ' Already exists ! <br />';
                    }
                } else {
                    echo $customer->ftth_id . ' No PPPOE Account ! <br />';
                }
            }
        }
        echo "Done";
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            $customer = Customer::find($request->input('id'));
            $customer->deleted = 1;
            $customer->update();
            if (RadiusController::checkRadiusEnable()) {
                RadiusController::deleteUser($customer->id);
            }
            return redirect()->back();
        }
    }
    public function subcomView($id)
    {
        $bundle_equiptments = BundleEquiptment::get();
        $customer = Customer::with(['township', 'package', 'status','isp','pop','dn','sn'])->findOrFail($id);
        
        return Inertia::render('Client/SubcomCustomerView', [
            'customer' => $customer,
            'bundle_equiptments'=>$bundle_equiptments
        ]);
    }

public function subcomUpdate(Request $request, $id)
{
    $customer = Customer::findOrFail($id);
    
    $validator = Validator::make($request->all(), [
        'installation_status' => 'required|array',
        'way_list_date' => 'nullable|date',
        'installation_date' => 'nullable|date',
        'fiber_distance' => 'nullable|integer',
        'bundles' => 'nullable',
        'onu_serial' => 'nullable|string',
        'onu_power' => 'nullable|string',
        'installation_remark' => 'nullable|string',
        'drum_no_txt' => 'nullable|string',
        'start_meter_txt' => 'nullable|string',
        'end_meter_txt' => 'nullable|string',
        'route_kmz_image' => 'nullable|image|max:10240',
        'drum_no_image' => 'nullable|image|max:10240',
        'start_meter_image' => 'nullable|image|max:10240',
        'end_meter_image' => 'nullable|image|max:10240',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator);
    }
    $oldCustomer = clone $customer;
    // Handle image uploads
    $imageFields = ['route_kmz_image', 'drum_no_image', 'start_meter_image', 'end_meter_image'];
    foreach ($imageFields as $imageField) {
        if ($request->hasFile($imageField)) {
            if ($customer->$imageField) {
                Storage::delete('public/' . $customer->$imageField);
            }
            $path = $request->file($imageField)->store('customer_images/' . $customer->id, 'public');
            $customer->$imageField = $path;
        }
    }

    $customer->installation_status = ($request->installation_status)?$request->installation_status['id']:null;
    $customer->way_list_date = $request->way_list_date;
    $customer->installation_date = $request->installation_date;
    $customer->fiber_distance = $request->fiber_distance;

    $customer->onu_serial = $request->onu_serial;
    $customer->onu_power = $request->onu_power;
    $customer->installation_remark = $request->installation_remark;
    $customer->drum_no_txt = $request->drum_no_txt;
    $customer->start_meter_txt = $request->start_meter_txt;
    $customer->end_meter_txt = $request->end_meter_txt;
    if (!empty($request->bundles)) {

        $customer->bundle = '';
        foreach ($request->bundles as $key => $value) {
            if ($key !== array_key_last($request->bundles))
                $customer->bundle .= $value['id'] . ',';
            else
                $customer->bundle .= $value['id'];
        }
    }
    $original = $customer->getOriginal();  // Get the original values before update
            $customer->update();                   // Perform the update
            $changes = $customer->getChanges();    // Get the updated values after the update
            app(\App\Services\CustomerHistoryService::class)->storeCustomerHistory(
                $customer,         // newly updated Customer
                $oldCustomer,      // old snapshot
                $changes,          // the changes array
                $id,
                $request->input('start_date') // optional
            );
            $logData = [];
            foreach ($changes as $key => $newValue) {
                $logData[$key] = [
                    'from' => $original[$key] ?? null,  // Original value
                    'to' => $newValue                   // New value
                ];
            }
            activity()
                ->causedBy(User::find(Auth::id()))
                ->performedOn($customer)
                ->withProperties(['changes' => $logData])  // Log the changes with from-to values
                ->log('Customer updated: ' . $customer->ftth_id);



    return redirect()->back()->with('message', 'Customer updated successfully');
}
    
}
